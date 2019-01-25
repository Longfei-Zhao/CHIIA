# encoding=utf-8
import MySQLdb
from log import logger
from bs4 import BeautifulSoup
db = MySQLdb.connect("localhost", "root", "root", "NLP", charset='utf8')
settings = {'id':None,'term':None,'startDate':None,'endDate':None}

def processItem(id,title,author,content,date,crawldate,url,source):
    """ put item into mysql database """
    
    try:
	table_content = processField(content,source)
	table_content['id'] = id
	table_content['date'] = date
	table_content['crawldate'] = crawldate
	table_content['content'] = MySQLdb.escape_string(content).decode('utf-8','ignore').encode("utf-8")
	table_content['url'] = MySQLdb.escape_string(url)
	table_content['source'] = MySQLdb.escape_string(source)
	table_content['title']  = MySQLdb.escape_string(title).decode('utf-8','ignore').encode("utf-8")
	table_content['author'] = MySQLdb.escape_string(author)
        key_list =''
	value_list = ''
	for key in table_content:
		key_list = key_list +',' + key
                value_list = value_list + ",'{}'".format(table_content[key])
	key_list=key_list[1:]
	value_list=value_list[1:]
	sql = "insert into NLP_ARTICLE({}) values({})".format(key_list,value_list)
#        print(key_list,value_list)
#        sql = "insert into NLP_ARTICLE(ID,title,author,content,date,crawldate,url,source) values('%s','%s','%s','%s','%s','%s','%s','%s')"
        params =(id, title, author,content, date,crawldate,url,source)
	# excute sql command
        cursor = db.cursor()
        cursor.execute(sql)
        # commit changes
        db.commit()
        return 1
    except Exception as e:
	logger.error('Cannot access database! Error Message:{}'.format(e))
        # Rollback in case there is any error
        db.rollback()
        return 0
    # shut donw database

def checkItemExist(id):
	sql = "select ID from NLP_ARTICLE where ID = '%s'" % id
	cursor = db.cursor()
	cursor.execute(sql)
	result = cursor.fetchall()
	if result:
		return True
	else:
		return False

def loadSettings():
	for key in settings:
		sql = "select {} from NLP_SPIDER order by id DESC limit 1".format(key)
        	cursor = db.cursor()
		cursor.execute(sql)
		if key == 'startDate':
			full_date = (cursor.fetchone())[0]
			logger.info('Load settings: startDate = {}'.format(full_date))
			settings[key] = {'date':full_date,'frd':full_date.day,'frm':full_date.month,'fry':full_date.year}
		elif key == 'endDate':
			full_date = (cursor.fetchone())[0]
			logger.info('Load settings: endDate = {}'.format(full_date))
			settings[key] = {'date':full_date,'tod':full_date.day,'tom':full_date.month,'toy':full_date.year}
		else:
        		settings[key] = (cursor.fetchone())[0]
			logger.info('Load settings: {} = {}'.format(key,settings[key]))
	return settings
def getTaskID():
	sql = "select id from NLP_SPIDER order by id DESC limit 1"
	cursor = db.cursor()
        cursor.execute(sql) 
	id = (cursor.fetchone())[0]
	return id
def getDatabase():
	return db
def updateProgress(progress):
        sql = "update NLP_SPIDER set progress={}  where id = {}".format(progress,settings['id']) 
        cursor = db.cursor()
        cursor.execute(sql)
	db.commit() 
def processField(html,source):
        table_content = dict()
        if source not in ['Publication','Dowjones']:
		return table_content 
	soup = BeautifulSoup(html,features="html.parser")
	for tr in soup.find_all('tr'):
    		field   = list(tr.children)[0].get_text(strip=True)
    		content = list(tr.children)[1].get_text(strip=True)
    		table_content[field] = MySQLdb.escape_string(content)
	#logger.info('{}'.format(table_content))
	table_content.pop('BY', None)
	table_content.pop('IN',None)
	return table_content

def getArticleByID(id):
	sql = "select ID,HD,LP,TD from NLP_ARTICLE where ID = '%s'" % id
	cursor = db.cursor()
	cursor.execute(sql)
	result = (cursor.fetchone())
	if result:
		article={'ID':result[0],'HD':result[1],'LP':result[2],'TD':result[3]}
		return article
	else:
		return
