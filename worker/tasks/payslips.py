import json
import os
import time
from io import BytesIO
import logging

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
    )


def fetch_tasks():
    try:
        conn = connect_to_db()
        cursor = conn.cursor()
        cursor.execute("SELECT id, file FROM ci_payslip_batches where processed = 0 LIMIT 1")
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
            page.save(output_file)

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
        extracted_data = [data for data in extracted_data if 'PUBC' not in data and 'PS15' not in data]

        full_string = extracted_data[2].split()
        split_string = full_string.split(" ", 1)

        number = split_string[0]

        dir_name, original_file_name = os.path.split(source_file)

        new_file_name = f'{number}.pdf'
        new_file_path = os.path.join(dir_name, new_file_name)

        os.rename(source_file, new_file_path)

        logger.info(f"File renamed from {original_file_name} to {new_file_name}")
    except Exception as e:
        logger.error(f"Error during PDF text extraction from {source_file}: {e}")







