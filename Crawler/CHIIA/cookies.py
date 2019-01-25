# encoding=utf-8

import os
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.keys import Keys
from selenium.common.exceptions import TimeoutException,NoSuchElementException,ElementNotVisibleException


import json
import logging




logger = logging.getLogger(__name__)
logging.getLogger("selenium").setLevel(logging.CRITICAL)  # 将selenium的日志级别设成DEBUG，太烦人

COOKIE_GETWAY = 'OUTSIDE'

anuID=[
       {'id':'u6274652','psw':'ly_game218'},
       ]

#Check Platform to load chromedriver
if os.name == 'nt':
    chrome_driver = os.getcwd() +"/chromedriver/win_chromedriver.exe"
else:
    chrome_driver = os.getcwd() +"/chromedriver/mac_chromedriver"

def get_cookie_without_login():
    #login by using headless chrome
    chrome_options = Options()
    chrome_options.add_argument("--headless")
    chrome_options.add_argument("--window-size=1920x1080")

    # download the chrome browser from https://sites.google.com/a/chromium.org/chromebrowser/downloads and put it in the
    # current directory
    
    
    FLAG_LOGIN = False
    while not FLAG_LOGIN:
        try:
            browser = webdriver.Chrome(chrome_options=chrome_options, executable_path=chrome_driver)
            browser.get("http://library-admin.anu.edu.au/tools/factiva-redirect")
            wait = WebDriverWait(browser, 10)
            input = wait.until(EC.presence_of_element_located((By.ID, 'ftx')))
            input = browser.find_element_by_id('ftx')
            input.send_keys('Chin* AND Australia AND invest*')
            search_button = browser.find_element_by_id('btnSearchBottom')
            search_button.click()
            headlineFrame = wait.until(EC.presence_of_element_located((By.ID, 'headlineFrame')))
            FLAG_LOGIN = True
        except NoSuchElementException:
            logger.warning("No Element")
        except TimeoutException:
            logger.warning("Timeout,See 'capture.png'")
            browser.get_screenshot_as_file("capture.png")
        logger.warning('Re-trying')

        list_cookies = browser.get_cookies()
        cookies=dict()
        for item in list_cookies:
            cookies[item['name']] = item['value']
        browser.close()
        return json.dumps(cookies)
def get_cookie_with_login(account,pwd):
    #login by using headless chrome
    chrome_options = Options()
    chrome_options.add_argument("--headless")
    chrome_options.add_argument("--window-size=1920x1080")
    
    # download the chrome browser from https://sites.google.com/a/chromium.org/chromebrowser/downloads and put it in the
    # current directory
    
    
    FLAG_LOGIN = False
    while not FLAG_LOGIN:
        try:
            browser = webdriver.Chrome(chrome_options=chrome_options, executable_path=chrome_driver)
            browser.get("http://library-admin.anu.edu.au/tools/factiva-redirect")
            wait = WebDriverWait(browser, 5)
            anuID = wait.until(EC.presence_of_element_located((By.ID, 'requester')))
            anuID.send_keys(account)
            password = browser.find_element_by_id('requesteremail')
            password.send_keys(pwd)
            browser.get_screenshot_as_file("logs/pre-login.png")
            password.send_keys(Keys.RETURN)
           
            wait = WebDriverWait(browser, 10)
            browser.get_screenshot_as_file("logs/login.png")
            btn = wait.until(EC.presence_of_element_located((By.ID, 'btnSearchBottom')))
            #swith from smart search to fix search
            switch = browser.find_element_by_id("switchbutton")
            switch.click()
            input = browser.find_element_by_id('ftx')
            input.send_keys('Chin* AND Australia AND invest*')
            search_button = browser.find_element_by_id('btnSearchBottom')
            search_button.click()
            headlineFrame = wait.until(EC.presence_of_element_located((By.ID, 'headlineFrame')))
            browser.get_screenshot_as_file("logs/search.png")
            FLAG_LOGIN = True
        except NoSuchElementException:
            print('No Element')
            browser.close()
        except ElementNotVisibleException:
            print('Not Visible')
            browser.close()
        except TimeoutException:
            print('Timeout')
            browser.get_screenshot_as_file("logs/capture.png")
            browser.close()
    list_cookies = browser.get_cookies()
    cookies=dict()
    for item in list_cookies:
        cookies[item['name']] = item['value']
    browser.close()
    return json.dumps(cookies)




def getCookie(account,password):
    if COOKIE_GETWAY == 'ANU':
        return get_cookie_without_login()
    elif COOKIE_GETWAY =='OUTSIDE':
        return get_cookie_with_login(account,password)
    else:
        logger.error("Please Set Cookie Gateway!")


def getCookies():
    """ 获取Cookies """
    logger.info("Get cookie...")
    cookies = []
    if COOKIE_GETWAY == 'ANU':
        cookie = get_cookie_without_login()
        cookies.append(cookie)
    elif COOKIE_GETWAY =='OUTSIDE':
        for elem in anuID:
            account = elem['id']
            password = elem['psw']
            cookie  =  get_cookie_with_login(account,password)
            if cookie != None:
                cookies.append(cookie)
    else:
        logger.error("Please Set Cookie Gateway!")

    return cookies

cookie = getCookies()
print(cookie)
logger.info("Get Cookies Finish!( Num:%d)" % len(cookie))
