# encoding=utf-8

import os
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.keys import Keys
from selenium.common.exceptions import TimeoutException,NoSuchElementException,ElementNotVisibleException,WebDriverException,UnexpectedAlertPresentException
from selenium.webdriver.support.ui import Select
from time import sleep
import re
import math
import json
from datetime import datetime
import cgi
from pipeline import processItem,checkItemExist,loadSettings,updateProgress
from dateutil.parser import parse
from log import logger
import sys
reload(sys)
sys.setdefaultencoding('utf-8')


anuID=[
       {'id':'u6274652','psw':'ly_game219'},
       ]

GATEWAY = 'ANULIB'

queryTerms = '((chin* or hong kong)) and (( (residential or site or commercial) and (casino resort or island or hotel or apartment or park or estate or property) and (group or firm or company or board or entitys) and (transaction* or purchase* or sale or sold or buy) ) or ( (uranium or wind or gold or solar or ore or copper or energy or alumina or iron or lead or coal or oil) and (bonds or acquisition or merge or purchase or sale or stake or equity) and (million* or billion* or B or M) and (operations or mining or firm or company)) or ( (dairy or cheese or butter or milk or bread or wine) and (sold or buy or sale or equity or stake or merge or acquire) and (brand or company or business or group or firm or board) and (million* or billion* or B or M))) not (terrorism or war or navy or stock market or share market or Wall St or Wall Street or Forex or Stock Exchange or rst=asxtex) and re=austr'
queryPeriod = 'In the last 5 years'

#Check Platform to load chromedriver
if os.name == 'nt':
	chrome_driver = os.getcwd() +"/chromedriver/win_chromedriver.exe"
elif os.name =='posix':
#	chrome_driver = os.getcwd() +"/chromedriver/linux_chromedriver"
        chrome_driver = os.getcwd() +"/chromedriver/mac_chromedriver"
#else:
#        chrome_driver = os.getcwd() +"/chromedriver/linux_chromedriver"
    


#login by using headless chrome
chrome_options = Options()
chrome_options.add_argument("--headless")
chrome_options.add_argument("--window-size=1920x1080")



def loginFectiva(browser,settings,account,pwd):
    FLAG_LOGIN = False
    while not FLAG_LOGIN:
        try:
            
            browser.get("http://library-admin.anu.edu.au/tools/factiva-redirect")
            
            if GATEWAY == 'OUTSIDE':
                logger.info("Gateway: OUTSIDE")
                wait = WebDriverWait(browser, 5)
                anuID = wait.until(EC.presence_of_element_located((By.ID, 'requester')))
                anuID.send_keys(account)
                password = browser.find_element_by_id('requesteremail')
                password.send_keys(pwd)
                browser.get_screenshot_as_file("logs/pre-login.png")
                password.send_keys(Keys.RETURN)
            elif GATEWAY == 'ANULIB':
                logger.info("Gateway: ANULIB")
            else:
                logger.error("Please Set Cookie Gateway!")
            logger.info('Start login fectiva...')
            wait = WebDriverWait(browser, 100)
            browser.get_screenshot_as_file("logs/login.png")
            btn = wait.until(EC.presence_of_element_located((By.ID, 'btnSearchBottom')))
            #select searching date
            dr = Select(browser.find_element_by_name('dr'))
            dr.select_by_visible_text('Enter date range...')
            #start date
            frd = browser.find_element_by_id('frd')
            frd.send_keys(settings['startDate']['frd'])
            frm = browser.find_element_by_id('frm')
            frm.send_keys(settings['startDate']['frm'])
            fry = browser.find_element_by_id('fry')
            fry.send_keys(settings['startDate']['fry'])
            #Enddate
            tod = browser.find_element_by_id('tod')
            tod.send_keys(settings['endDate']['tod'])
            tom = browser.find_element_by_id('tom')
            tom.send_keys(settings['endDate']['tom'])
            toy = browser.find_element_by_id('toy')
            toy.send_keys(settings['endDate']['toy'])
            filter = Select(browser.find_element_by_name('isrd'))
            filter.select_by_visible_text('Off')
            browser.execute_script('document.getElementById("ftx").value="{}";doLinkSubmit("../ha/default.aspx");'.format(settings['term']))
            headlineFrame = wait.until(EC.presence_of_element_located((By.ID, 'headlineFrame')))
            browser.get_screenshot_as_file("logs/search.png")
            FLAG_LOGIN = True
        except NoSuchElementException:
            logger.error('No Element during login')
            browser.close()
        except ElementNotVisibleException:
            logger.error('Not Visible during login')
#            browser.close()
        except TimeoutException:
            logger.error('Timeout during login')
            browser.get_screenshot_as_file("logs/Timeout.png")
            #browser.close()
    list_cookies = browser.get_cookies()
    cookies=dict()
    for item in list_cookies:
        cookies[item['name']] = item['value']

    return json.dumps(cookies)
def getOverview(browser,settings):
    totalWebNews = browser.find_element_by_xpath('//span[@data-channel="Website"][1]/a/span[@class="hitsCount"]').text.replace(',','')
    totalWebNews = int(re.search(r'\((.*)\)',totalWebNews).group(1))
    totalBlogs = browser.find_element_by_xpath('//span[@data-channel="Blog"][1]/a/span[@class="hitsCount"]').text.replace(',','')
    totalBlogs = int(re.search(r'\((.*)\)',totalBlogs).group(1))
    totalFectiva = browser.find_element_by_xpath('//span[@data-channel="Dowjones"][1]/a/span[@class="hitsCount"]').text.replace(',','')
    totalFectiva = int(re.search(r'\((.*)\)',totalFectiva).group(1))
    totalPublication = browser.find_element_by_xpath('//span[@data-channel="Publication"][1]/a/span[@class="hitsCount"]').text.replace(',','')
    totalPublication = int(re.search(r'\((.*)\)',totalPublication).group(1))
    maxPage = max(totalWebNews,totalBlogs,totalFectiva,totalPublication)/100
    timePeriod = (settings['endDate']['date']-settings['startDate']['date'])
    timesplit = int(math.ceil(maxPage / 100.0))
    logger.info('split:{}'.format(timesplit))
    endDateSplit=[settings['startDate']['date'] + timePeriod / (split+1)  for split in range(timesplit)]
    endDateSplit.reverse()
    startDateSplit = [settings['endDate']['date'] - timePeriod / (split+1)   for split in range(timesplit)]
    logger.info('Summary: there are {} in WebNews, {} in Blog, {} in Dowjones, {} in Publication; Maximum pages:{} timePeriod:{},startDateSplit: {}'.format(totalWebNews,totalBlogs,totalFectiva,totalPublication,maxPage,endDateSplit,startDateSplit))
    
    status={'crawled_pages': 0,'totalArticles': totalWebNews + totalBlogs + totalFectiva + totalPublication}
    return [(start,end) for start,end in zip(startDateSplit,endDateSplit)],status
def getStatus(browser):
    #Compute the total pages we need to download
    
    pageInfo = browser.find_element_by_xpath('//span[@class="resultsBar"]').text.replace(',','')
    currentPage = int(int(re.search(r'Headlines (.*) - (.*) of (.*)',pageInfo).group(1))/100)
    articlesInThisPage = int(re.search(r'Headlines (.*) - (.*) of (.*)',pageInfo).group(2)) - int(re.search(r'Headlines (.*) - (.*) of (.*)',pageInfo).group(1)) + 1
    totalPages = math.ceil((int(re.search(r'Headlines (.*) - (.*) of (.*)',pageInfo).group(3)))/100.0)-1
    nextPageStart = int(re.search(r'Headlines (.*) - (.*) of (.*)',pageInfo).group(2))+1
    
    totalArticles = browser.find_element_by_xpath('//span[@data-channel="All"][1]/a/span[@class="hitsCount"]').text.replace(',','')
    totalArticles = int(re.search(r'\((.*)\)',totalArticles).group(1))
    totalArticles = totalArticles #- totalBlogs #- totalWebNews
    '''Minus WebNews'''

    return currentPage,totalPages,nextPageStart,totalArticles,articlesInThisPage

def getArticleInfo(browser,id, source):
    headline = browser.find_element_by_xpath('//div[@id="headlines"]/table/tbody/tr[{}]/td[3]/a'.format( id ))
    documentID = browser.find_element_by_xpath('//div[@id="headlines"]/table/tbody/tr[{}]/td[3]/div[3]'.format(id)).text
    documentID =  re.search(r'\(Document (.*)\)',documentID).group(1)
    documentType = browser.find_element_by_xpath('//div[@id="headlines"]/table/tbody/tr[{}]/td[3]/img'.format(id)).get_attribute('title')
    leadFields = browser.find_element_by_xpath('//div[@id="headlines"]/table/tbody/tr[{}]/td[3]/div[@class="leadFields"]'.format(id)).text.split(',')
    author = leadFields[0]
    for date in leadFields:
	try:
            parse(date).strftime('%Y-%m-%d')
            return headline,date,author,documentID,documentType 
        except:
            pass
def saveCheckPoint(checkpoint):
    f=open('checkpoint/checkpoint.json','w')
    json.dump(checkpoint,f)
    f.close()
    logger.info('Save checkpoint at {}'.format(checkpoint))
def loadCheckPoint():
    try:
        f=open('checkpoint/checkpoint.json','r')
        checkpoint = json.load(f)
        f.close()
        logger.info('Load checkpoint from file: {}'.format(checkpoint))
    except:
        logger.info('No checkpoint has been founded, Create a new checkpoint!')
        checkpoint = {'Dowjones':0, 'Publication':0, 'Website': 0,'Blog':0}

    return checkpoint
def resetCheckPoint():
    checkpoint = {'Dowjones':0, 'Publication':0, 'Website': 0,'Blog':0}
    saveCheckPoint(checkpoint)
    logger.info('Reset checkpoint')
def crawlFectiva(browser,checkpoint,status):

#select = Select(browser.find_element_by_name('hso'))
#select.select_by_visible_text('Sort by: Oldest first')
    for source in ['Blog','Website','Dowjones','Publication']:
        logger.info('Start crawling articles from: {}...'.format(source))        
        articlesOfChannel = browser.find_element_by_xpath('//span[@data-channel="{}"][1]/a/span[@class="hitsCount"]'.format(source)).text.replace(',','')
        articlesOfChannel = int(re.search('\((.*)\)',articlesOfChannel).group(1)) 
	if articlesOfChannel ==0:
        	logger.info('No articles in {} channel.'.format(source))
                logger.info('End of Source from {}'.format(source))
        	continue    
        
        dataChannel = browser.find_element_by_xpath('//span[@data-channel="{}"]'.format(source))
        dataChannel.click()
        wait = WebDriverWait(browser, 10)
        btn = wait.until(EC.presence_of_element_located((By.XPATH, '//span[@class="tabOn"][@data-channel="{}"]'.format(source))))
        #Compute the total pages we need to download
        currentPage,totalPages,nextPageStart,totalArticles,articlesInThisPage = getStatus(browser)
        #Load checkpoint
	checkpoint = loadCheckPoint()
        logger.info('Total pages:{} , currentAt:{} , checkPointAt:{}'.format(totalPages,currentPage,checkpoint[source]))
        while currentPage != totalPages or checkpoint[source]!=currentPage or totalPages == 0:
            logger.info('Total pages:{} , currentAt:{} , checkPointAt:{}'.format(totalPages,currentPage,checkpoint[source]))
            for i in range(abs(checkpoint[source] - currentPage)):
                #Compute the total pages we need to download
                currentPage,totalPages,nextPageStart,totalArticles,articlesInThisPage = getStatus(browser)
                logger.info('Skip Page To checkPoint...Total pages:{} , currentAt:{} , checkPointAt:{}'.format(totalPages,currentPage,checkpoint[source]))
                btn_nextpage = browser.find_element_by_xpath('//a[@class="nextItem"]')
                btn_nextpage.click()
                wait.until(EC.text_to_be_present_in_element((By.XPATH, '//div[@id="headlines"]/table/tbody/tr[@class="headline"][1]/td[@class="count"]'), '{}.'.format(nextPageStart) ))
		status['crawled_pages'] += 100

            #Compute the total pages we need to download
            currentPage,totalPages,nextPageStart,totalArticles,articlesInThisPage = getStatus(browser)

            for id in range(1,articlesInThisPage + 1):
                status['crawled_pages'] +=1
		updateProgress(min(status['crawled_pages']/float(status['totalArticles']),99.0))
                headline,date,author,documentID,documentType = getArticleInfo(browser,id,source)
                if checkItemExist(documentID):
                    logger.info('{:.1%} item {} exist in database skip to next one.'.format(status['crawled_pages']/float(status['totalArticles']),id))
                    continue     
                if documentType == 'Factiva Licensed Content':
                    logger.info('{:.1%} [DOC] Get {} of {} in page {}.Totally {} pages {} articles'.format(status['crawled_pages']/float(status['totalArticles']),id,articlesInThisPage, currentPage,totalPages,totalArticles))
                    
                    logger.debug('id:{}, documentID:{}, Headline:{}, date:{}, author:{} '.format(id,documentID,headline.text,date,author))
                    headline.click()
                    logger.debug('waiting content response')
                    wait.until(EC.text_to_be_present_in_element((By.XPATH, '//div[@id="artHdr1"]/span[1]'), 'Article {}'.format(currentPage * 100 + id) ))
                    logger.debug('get content response')
                    articleHtml = browser.find_element_by_xpath('//div[@class="article enArticle"]')
                    title =headline.text
                    content = articleHtml.get_attribute('innerHTML')
                    
                    date = parse(date).strftime('%Y-%m-%d')
                    crawldate = parse(str(datetime.now())).strftime('%Y-%m-%d')
                    url = ''
                    processItem(documentID,title,author,content,date,crawldate,url,source)
                    sleep(2)
                if documentType == 'HTML':
                    logger.info('{:.1%} [HTM]Get {} of 100 in page {}.Totally {} pages {} articles'.format(status['crawled_pages']/float(status['totalArticles']),id, currentPage,totalPages,totalArticles))
                    browser.set_page_load_timeout(6)
                    try: 
                    	headline.click()
                    	title = headline.text
                    	window_main = browser.window_handles[0]
                    	window_download = browser.window_handles[-1]
                    	browser.switch_to_window(window_download)
                    	sleep(4)
                    	url = browser.current_url
                    	logger.debug('Try to get html page source')
                        content = browser.page_source
                        logger.debug('Get website success.')
                    except:
			url = browser.current_url
                        content = "<h1><a href='{}'>baidu</a></h1>".format(url)
                        logger.warning('No response from {} of page {} title:{}, url:{}'.format(documentID,currentPage,title,url))
                        
                    logger.debug('id:{}, documentID:{}, Headline:{}, date:{}, author:{} '.format(id,documentID,title,date,author))
                    date = parse(date).strftime('%Y-%m-%d')
                    crawldate = parse(str(datetime.now())).strftime('%Y-%m-%d')
                    processItem(documentID,title,author,content,date,crawldate,url,source)
                    try:
                        browser.execute_script('window.close();')     
                    except:
                        logger.debug('Close tab error')               
                    browser.switch_to_window(window_main)
                    
                    

         
              #sometimes recaptcha occurs here
              #view next page
            
            if currentPage == totalPages:
                logger.info('End of Source from {}'.format(source))
                break
            
            checkpoint[source] = currentPage
            saveCheckPoint(checkpoint)

            btn_nextpage = browser.find_element_by_xpath('//a[@class="nextItem"]')
            btn_nextpage.click()
            wait.until(EC.text_to_be_present_in_element((By.XPATH, '//div[@id="headlines"]/table/tbody/tr[@class="headline"][1]/td[@class="count"]'), '{}.'.format(nextPageStart) ))


settings = loadSettings()
browser = webdriver.Chrome(chrome_options=chrome_options, executable_path=chrome_driver)
loginFectiva(browser,settings,'','')

timeSplit,status = getOverview(browser,settings)
logger.info('Time split:{}'.format(timeSplit))
browser.close()
for (start,end) in timeSplit:
        logger.info('Now start from {} to {}.'.format(str(start),str(end)))
	settings['startDate']['frd'] =  start.day
        settings['startDate']['frm'] =  start.month
        settings['startDate']['fry'] =  start.year
        settings['endDate']['tod'] = end.day
        settings['endDate']['tom'] = end.month
        settings['endDate']['toy'] = end.year


	while True:
		checkpoint = loadCheckPoint()
        	browser = webdriver.Chrome(chrome_options=chrome_options, executable_path=chrome_driver)	
        	loginFectiva(browser,settings,'','')
		try:
    			crawlFectiva(browser,checkpoint,status)
                        resetCheckPoint()
                        
			break;
		except TimeoutException as e:
			logger.error('Timeout during crawling pages, error message:{}'.format(e))
			browser.close()
		except UnexpectedAlertPresentException:
    			logger.error('Fectiva alert:We are unable to process your request at this time.  Please try again in a few minutes.')
			browser.close()
		except Exception as e:
			logger.error('Critical error occured, because {}. restart crawler'.format(e))
			browser.close()
logger.info('Finish!')
browser.close()


#print(cookie)
#logger.info("Get Cookies Finish!( Num:%d)" % len(cookie))
