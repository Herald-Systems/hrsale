import logging, os
from celery import Celery
from celery.signals import setup_logging

import logging
import logging.handlers

from tasks.payslips import fetch_tasks, split_file_by_page, mark_task_as_processed, fetch_employees, process_payslip

# Create a Celery application instance
app = Celery('tasks', broker='redis://localhost:6379/0')
app.conf.update(
    enable_utc=False,
    timezone='Africa/Nairobi'
)

# Configure logging
@setup_logging.connect
def configure_logging(loglevel=None, logfile=None, format=None, colorize=None, **kwargs):
    log_directory = "logs"
    if not os.path.exists(log_directory):
        os.makedirs(log_directory)

    logger.warning(f"Log Level {loglevel}")


    # Use Celery's provided logfile path if available, otherwise default
    log_filename = logfile if logfile else os.path.join(log_directory, "celery_worker.log")

    # Use Celery's provided log level if available, otherwise default
    effective_log_level = loglevel if loglevel else logging.INFO

    # Use Celery's provided format if available, otherwise default
    log_format_str = format if format else '%(asctime)s - %(name)s - %(levelname)s - %(message)s'
    log_formatter = logging.Formatter(log_format_str)

    root_logger = logging.getLogger()
    root_logger.setLevel(effective_log_level)

    # Optional: Clear existing handlers added by Celery if you want ONLY your file handler
    # Be careful with this, as it might remove the console logger too.
    # You might want to keep the console logger for debugging.
    # root_logger.handlers.clear()

    # Check if a similar file handler already exists to avoid duplicates
    if not any(isinstance(h, logging.handlers.TimedRotatingFileHandler) and h.baseFilename == log_filename for h in root_logger.handlers):
        # --- Timed Rotating File Handler ---
        file_handler = logging.handlers.TimedRotatingFileHandler(
            filename=log_filename,
            when='D',
            interval=1,
            backupCount=5,
            encoding='utf-8',
            delay=False,
            utc=False
        )
        file_handler.setFormatter(log_formatter)
        root_logger.addHandler(file_handler)

        print(f"****** Added TimedRotatingFileHandler for {log_filename} ******") # Add print for confirmation
    # --- End of your logging setup code ---

logger = logging.getLogger(__name__)

# Use dotenv to load environment variables if necessary
from dotenv import load_dotenv

# Load environment variables from the .env file if present
load_dotenv()

current_directory = os.getcwd()

@app.task(name="process_tasks")
def process_tasks():
    logger.info('Processing tasks')

    try:
        tasks = fetch_tasks()
        for task in tasks:
            task_id, file_path = task['id'], task['file']
            logger.info(f"Processing task {task_id} with file path: {file_path}")

            prefix = "https://espahrp.echadconsultants.com/writable/uploads/"
            output_folder = os.path.join("/var/www/html/www/writable/uploads", str(task_id))
            os.makedirs(output_folder, exist_ok=True)

            input_file_url = f"{prefix}{file_path}"
            logger.info(f"Splitting file {input_file_url} into {output_folder}")

            try:
                split_file_by_page(input_file_url, output_folder)

                mark_task_as_processed(task_id)

            except Exception as e:
                logger.error(f"Error processing file {input_file_url} for task {task_id}: {e}", exc_info=True)

            process_payslips(task['pay_date'])
    except Exception as e:
        logger.error(f"Error fetching or processing tasks: {e}", exc_info=True)


@app.task(name="process_payslips")
def process_payslips(pay_date):
    logger.info("Processing payslips")
    employees = fetch_employees()
    for employee in employees:
        process_payslip(employee, pay_date)


@app.on_after_configure.connect
def setup_periodic_tasks(sender, **kwargs):
    sender.add_periodic_task(
        60.0,
        process_tasks.s(),
        name='add every 5 minutes'
    )
