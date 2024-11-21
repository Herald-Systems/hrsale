import logging, os
from celery import Celery


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