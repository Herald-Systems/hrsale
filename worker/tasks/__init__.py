import logging, os
from celery import Celery

from tasks.payslips import fetch_tasks, split_file_by_page

# Create a Celery application instance
app = Celery('tasks', broker='redis://localhost:6379/0')
app.conf.update(
    enable_utc=False,
    timezone='Africa/Nairobi'
)

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

current_directory = os.getcwd()

@app.task(name="process_tasks")
def process_tasks():
    logger.info('Processing tasks')

    try:
        tasks = fetch_tasks()
        for task in tasks:
            task_id, file_path = task
            logger.info(f"Processing task {task_id} with file path: {file_path}")

            prefix = "https://espahrp.echadconsultants.com/writable/uploads/"
            output_folder = os.path.join("/var/www/html/www/writable/uploads", str(task_id))
            os.makedirs(output_folder, exist_ok=True)

            input_file_url = f"{prefix}{file_path}"
            logger.info(f"Splitting file {input_file_url} into {output_folder}")

            try:
                split_file_by_page(input_file_url, output_folder)
            except Exception as e:
                logger.error(f"Error processing file {input_file_url} for task {task_id}: {e}", exc_info=True)
    except Exception as e:
        logger.error(f"Error fetching or processing tasks: {e}", exc_info=True)


@app.on_after_configure.connect
def setup_periodic_tasks(sender, **kwargs):
    sender.add_periodic_task(
        60.0,
        process_tasks.s(),
        name='add every 1 minute'
    )
