import logging 
from  datetime import datetime
import time
import MySQLdb
class LogToMysqlHandler(logging.Handler):
    '''
    Customized logging handler that puts logs to the database.
    '''
    def __init__(self,db_tbl ,db_tbl_field):
        logging.Handler.__init__(self)
        self.sql_conn = MySQLdb.connect("localhost", "root", "root", "NLP", charset='utf8')
        self.db_tbl = db_tbl
	self.db_tbl_field = db_tbl_field
	self.db_task_id = self.getTaskID()
    	self.log_content = ''
    def emit(self, record):
        # Set current time
        tm = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(record.created))
        # Clear the log message so it can be put to db via sql (escape quotes)
        log_msg = record.msg
        log_msg = log_msg.strip()
        log_msg = log_msg.replace('\'', '\'\'')
	log_level =record.levelname
	log_name =record.name
        # Make the SQL insert
        # If error - print it out on screen. Since DB is not working - there's
        # no point making a log about it to the database :)
	new_log = '[{}][{}] ##{} \n'.format(tm,log_level,log_msg)

	self.log_content += MySQLdb.escape_string(new_log)
	sql = "update {} set {}='{}'  where id = {}".format(self.db_tbl,self.db_tbl_field,self.log_content,self.db_task_id)
	try:
		cursor = self.sql_conn.cursor()
		cursor.execute(sql)
		self.sql_conn.commit()
	except Exception as e:
#		logger.error('Error during update log into mysql msg:{}'.format(e))
		pass
    def getTaskID(self):
        sql = "select id from NLP_SPIDER order by id DESC limit 1" 
	cursor = self.sql_conn.cursor()
	cursor.execute(sql)
	id = (cursor.fetchone())[0] 
	return id	
    def __del__( self ):  
	self.sql_conn.close()

logging.getLogger("selenium").setLevel(logging.CRITICAL)
   
logger = logging.getLogger('cralwer') 
logger.setLevel(logging.DEBUG) 

fh = logging.FileHandler('logs/{}.log'.format(str(datetime.now()))) 
fh.setLevel(logging.DEBUG) 
   
ch = logging.StreamHandler() 
ch.setLevel(logging.INFO) 

dh = LogToMysqlHandler('NLP_SPIDER','log')
dh.setLevel(logging.INFO)
formatter = logging.Formatter('[%(asctime)s][%(levelname)s] ## %(message)s')
fh.setFormatter(formatter) 
ch.setFormatter(formatter) 
   
logger.addHandler(fh) 
logger.addHandler(ch) 
logger.addHandler(dh)

