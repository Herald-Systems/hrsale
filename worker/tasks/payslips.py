import os
import pymysql
import logging


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
    except pymysql.MySQLError as e:
        logger.error(f"Database error: {e}")
        tasks = []
    return tasks