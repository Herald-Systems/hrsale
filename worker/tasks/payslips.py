import os
import time
from io import BytesIO
import logging
from datetime import datetime
from decimal import Decimal, ROUND_HALF_UP
import os

import fitz  # PyMuPDF
import pymysql
from bs4 import BeautifulSoup
import requests

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)


def connect_to_db():
    return pymysql.connect(
        host=os.environ.get('DB_HOST', 'localhost'),
        user=os.environ.get('DB_USERNAME', 'root'),
        password=os.environ.get('DB_PASSWORD', '1234'),
        db=os.environ.get('DB_NAME', ''),
        port=int(os.environ.get('DB_PORT', 3306)),
        cursorclass=pymysql.cursors.DictCursor
    )

def fetch_employees():
    try:
        conn = connect_to_db()
        cursor = conn.cursor()
        cursor.execute("SELECT ci_erp_users.*, ci_erp_users_details.* FROM ci_erp_users, ci_erp_users_details where ci_erp_users.user_type = 'staff' AND ci_erp_users.user_id = ci_erp_users_details.user_id AND ci_erp_users_details.calculate_payroll = 1")
        employees = cursor.fetchall()
        cursor.close()
        conn.close()
        return employees
    except pymysql.MySQLError as e:
        logger.error(f"Database error: {e}")
        return []

def fetch_loans(user_id):
    try:
        conn = connect_to_db()
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM ci_erp_users_loans WHERE user_id = %s", (user_id,))
        loans = cursor.fetchall()
        cursor.close()
        conn.close()
        return loans
    except pymysql.MySQLError as e:
        logger.error(f"Database error: {e}")
        return []

def calculate_resident_gross_tax(n):
    """ Calculates Gross Tax for Residents based on N """
    n = Decimal(n)

    if n <= Decimal(20000):
        return n * Decimal(0)
    elif n <= Decimal(33000):
        return n * Decimal(0.30) - Decimal(6000)
    elif n <= Decimal(70000):
        return n * Decimal(0.35) - Decimal(6650)
    elif n <= Decimal(250000):
        return n * Decimal(0.40) - Decimal(11150)
    else: # over 250000
        return n * Decimal(0.42) - Decimal(16150)

def calculate_non_resident_gross_tax(n):
    """ Calculates Gross Tax for Non-Residents based on N """
    n = Decimal(n)
    if n <= Decimal(33000):
        return n * Decimal(0.30)
    elif n <= Decimal(70000):
        return n * Decimal(0.35) - Decimal(1650)
    elif n <= Decimal(250000):
        return n * Decimal(0.40) - Decimal(5150)
    else: # over 250000
        return n * Decimal(0.42) - Decimal(10150)

def calculate_dependent_rebate(dependents, gross_tax, fortnight_salary):
    """ Calculates the dependent rebate for residents with declaration """
    rebate = Decimal(0)
    # Note: Interpretation of the second part ("and If fortnight income exceeds K1,269...")
    # is ambiguous. This implementation calculates the rebate based *only* on the
    # "Max of (KXX or Min(Percentage*Gross Tax or Cap))" part.
    # The K1269 condition might apply differently or be a separate adjustment.
    # Please clarify if the K17.31/K28.85/K40.38 part should be added/subtracted here.

    if dependents == 1:
        rebate = max(Decimal(45), min(Decimal(0.15) * gross_tax, Decimal(450)))
        # if fortnight_salary > 1269:
        #     rebate += 17.31 # Or some other logic based on clarification
    elif dependents == 2:
        rebate = max(Decimal(75), min(Decimal(0.25) * gross_tax, Decimal(750)))
        # if fortnight_salary > 1269:
        #     rebate += 28.85 # Or some other logic
    elif dependents >= 3:
        rebate = max(Decimal(105), min(Decimal(0.35) * gross_tax, Decimal(1050)))
        # if fortnight_salary > 1269:
        #     rebate += 40.38 # Or some other logic

    # Ensure rebate is not negative
    return max(Decimal(0), rebate)


def process_payslip(employee, pay_date):
    # pay_date = datetime.strptime(pay_date, '%Y-%m-%d')

    logger.info(f"Processing payslip for employee {employee.get('first_name')} For {pay_date.strftime('%B %Y')}")

    annual_salary = employee.get('basic_salary', 0)
    total_allowances = Decimal(0)
    is_resident = bool(employee.get('resident', False))
    dependent_declaration_logged = bool(employee.get('dependent_declaration_logged', False))
    dependents = int(employee.get('number_of_children', 0))
    pos_voluntary_super = employee.get('pos_voluntary_super', 0)
    loan_amount = Decimal(0)

    loans = fetch_loans(employee.get('user_id'))

    for loan in loans:
        loan_amount += loan.get('amount', 0)

    # --- Calculations ---
    # annual_salary = employee.get('basic_salary', 0) * 12
    fortnight_salary = (annual_salary * 10) / 261 if annual_salary > 0 else 0
    # Overtime is defined but not added to Gross Salary per the rules provided.
    # overtime = (annual_salary / 261) * (10 / 73.5) if annual_salary > 0 else 0
    gross_salary = fortnight_salary + total_allowances

    gross_tax = 0
    net_tax = 0
    dependent_rebate = 0

    if is_resident:
        if dependent_declaration_logged:
            # Calculate N for resident tax
            n_resident = (fortnight_salary * Decimal(26)) - Decimal(200)
            gross_tax = calculate_resident_gross_tax(n_resident)
            # Calculate dependent rebate
            dependent_rebate = calculate_dependent_rebate(dependents, gross_tax, fortnight_salary)
            # Ensure gross_tax after rebate is not negative before division
            net_tax = max(Decimal(0), gross_tax - dependent_rebate) / 26
        else:
            # Dependent Declaration Logged (No) logic
            income_no_declaration = (fortnight_salary * Decimal(26))
            gross_tax = income_no_declaration * Decimal(0.42) # Direct calculation as per rule
            net_tax = gross_tax / 26
    else:
        # Non-Resident Tax Calculation
        n_non_resident = fortnight_salary * Decimal(26)
        gross_tax = calculate_non_resident_gross_tax(n_non_resident)
        net_tax = gross_tax / 26

    # Ensure net_tax is not negative
    net_tax = max(Decimal(0), net_tax)

    # Superannuation
    super_employer_contribution = fortnight_salary * Decimal(0.084)
    super_employee_compulsory = fortnight_salary * Decimal(0.06)
    super_employee_voluntary = pos_voluntary_super # From employee's choice
    total_super_employee = super_employee_compulsory + super_employee_voluntary

    # Total Deductions from Gross Salary
    total_deductions = net_tax + total_super_employee + loan_amount

    # Net Salary
    net_salary = gross_salary - total_deductions

    payslip = {
        'payslip_key': f"{employee.get('employee_id', 'KEY_ERR')}_{pay_date.strftime('%Y%m')}", # Unique key
        'company_id': employee.get('company_id'),
        'staff_id': employee.get('user_id'), # Assuming this maps to the employee being processed
        'salary_month': pay_date.strftime('%B %Y'), # Dynamic month/year
        'wages_type': 1, # Assuming 1 means salary
        'payslip_type': 'Fortnightly', # Changed from 'Full Monthly' based on calculations
        'basic_salary': fortnight_salary, # Using fortnight salary as basic for this period
        'annual_salary': annual_salary, # Store annual salary for reference
        'daily_wages': 0, # Or calculate if needed: annual_salary / 261
        'hours_worked': 0, # Or track if needed
        'total_allowances': total_allowances,
        'gross_salary': gross_salary, # Calculated Gross
        'total_commissions': 0, # Add if applicable
        'gross_tax': gross_tax, # Calculated Gross Tax (before rebate/division)
        'dependent_rebate': dependent_rebate, # Calculated Rebate
        'net_tax': net_tax, # Calculated Net Tax (for the period)
        'super_employee_compulsory': super_employee_compulsory,
        'super_employee_voluntary': super_employee_voluntary,
        'total_super_employee': total_super_employee, # Employee part (deducted)
        'super_employer_contribution': super_employer_contribution, # Employer part (not deducted from employee)
        'total_statutory_deductions': net_tax + total_super_employee, # Sum of tax and employee super
        'total_other_payments': 0, # Add other payments if applicable
        'total_deductions': total_deductions, # All deductions summed up
        'net_salary': net_salary, # Final calculated net salary
        'payment_method': 1, # Default or from employee data
        'pay_comments': f"System generated at {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}",
        'is_payment': True, # Assuming payment is made
        'year_to_date': 0, # This would typically require fetching previous payslips
        'is_advance_salary_deduct': False,
        'advance_salary_amount': 0,
        'is_loan_deduct': loan_amount > 0,
        'loan_amount': loan_amount,
        'status': 1, # Assuming 1 means active/processed
        'created_at': datetime.now().strftime('%Y-%m-%d %H:%M:%S'),
    }

    payslip_for_db = payslip.copy()

    for key, value in payslip_for_db.items():
        if isinstance(value, bool):
            payslip_for_db[key] = 1 if value else 0

    # Dynamically create the INSERT statement
    columns = ', '.join(payslip_for_db.keys())
    placeholders = ', '.join(['%s'] * len(payslip_for_db))
    sql = f"INSERT INTO ci_payslips ({columns}) VALUES ({placeholders})"
    values = list(payslip_for_db.values())


    try:
        conn = connect_to_db()
        cursor = conn.cursor()
        logger.info(f"Executing SQL: {sql}")
        cursor.execute(sql, values)
        conn.commit()
        logger.info(f"Successfully inserted payslip for staff_id {payslip.get('staff_id')}")
    except pymysql.MySQLError as e:
        logger.error(f"Failed to insert payslip as processed: {e}")
        return None
    except Exception as e:
        logger.error(f"Error during payslip processing: {e}")
        return None
    finally:
        if 'cursor' in locals() and cursor:
            cursor.close()
        if 'conn' in locals() and conn:
            conn.close()
    return payslip


def fetch_tasks():
    try:
        conn = connect_to_db()
        cursor = conn.cursor()
        cursor.execute("SELECT id, file, pay_date FROM ci_payslip_batches where processed = 0 LIMIT 1")
        tasks = cursor.fetchall()
        cursor.close()
        conn.close()
        return tasks
    except pymysql.MySQLError as e:
        logger.error(f"Database error: {e}")
        return []

def mark_task_as_processed(task_id):
    timestamp = time.strftime('%Y-%m-%d %H:%M:%S')
    try:
        conn = connect_to_db()
        cursor = conn.cursor()
        cursor.execute("UPDATE ci_payslip_batches SET processed = 1, processed_at = %s WHERE id = %s", (timestamp, task_id))
        conn.commit()
        cursor.close()
        conn.close()
    except pymysql.MySQLError as e:
        logger.error(f"Failed to update task {task_id} as processed: {e}")


def split_file_by_page(source_file, output_folder):
    try:
        pdf_stream = BytesIO()

        # Download the PDF from URL
        response = requests.get(source_file)
        pdf_stream.write(response.content)

        # Make sure the buffer is at the start
        pdf_stream.seek(0)

        # Load the PDF with PyMuPDF
        pdf_document = fitz.open(stream=pdf_stream, filetype="pdf")

        os.makedirs(output_folder, exist_ok=True)

        # Iterate through the pages and save each page as a separate PDF
        for page_number in range(pdf_document.page_count):
            page = pdf_document.load_page(page_number)
            output_file = os.path.join(output_folder, f"page_{page_number + 1}.pdf")

            # Create a new document and insert the page
            single_page_doc = fitz.open()
            single_page_doc.insert_pdf(pdf_document, from_page=page_number, to_page=page_number)

            # Save the single-page document
            single_page_doc.save(output_file)
            single_page_doc.close()

            extract_dynamic_text_from_pdf(output_file)

        logger.info(f"Split {source_file} into {pdf_document.page_count} pages in {output_folder}")
    except Exception as e:
        logger.error(f"Error during PDF processing: {e}")


def extract_dynamic_text_from_pdf(source_file):
    try:
        pdf_stream = BytesIO()

        with open(source_file, 'rb') as f:
            pdf_stream.write(f.read())
        pdf_stream.seek(0)

        # Load the PDF with PyMuPDF
        pdf_document = fitz.open(stream=pdf_stream, filetype="pdf")

        # Extract XHTML text from the first page
        xhtml_text = pdf_document[0].get_text('xhtml')

        # Parse the XHTML with BeautifulSoup
        soup = BeautifulSoup(xhtml_text, 'html.parser')

        extracted_data = []

        for p_tag in soup.find_all('p'):
            if p_tag.tt:
                label_text = p_tag.tt.get_text()  # Get the label text
                extracted_data.append(label_text)

        labels_to_remove = ["Pay Date:", "Award:", "Classification:", "Base Salary(PGK):", "PUBC PS15"]
        extracted_data = [data for data in extracted_data if data not in labels_to_remove]
        extracted_data = [data for data in extracted_data if not data.startswith('PUB') and not data.startswith('PS')]

        print(extracted_data[2])

        full_string = extracted_data[2]
        split_string = full_string.split(" ", 1)

        number = split_string[0]

        dir_name, original_file_name = os.path.split(source_file)

        new_file_name = f'{number}.pdf'
        new_file_path = os.path.join(dir_name, new_file_name)

        os.rename(source_file, new_file_path)

        logger.info(f"File renamed from {original_file_name} to {new_file_name}")
    except Exception as e:
        logger.error(f"Error during PDF text extraction from {source_file}: {e}")







