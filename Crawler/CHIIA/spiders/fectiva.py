# -*- coding: utf-8 -*-
import scrapy
from datetime import datetime as dt
from dateutil.parser import parse
from scrapy.http import Request
from scrapy.http import FormRequest
from bs4 import BeautifulSoup
import re
from CHIIA.items import ArticleItem,PDFItem
import logging
logger = logging.getLogger(__name__)


class FectivaSpider(scrapy.Spider):
    name = 'fectiva'
    allowed_domains = ['factiva.com','virtual.anu.edu.au']
    page = 0
    #i=0
    
    def start_requests(self):
        post_data = "ftx=Australia"
        search_url = 'https://global-factiva-com.virtual.anu.edu.au/ha/default.aspx'
        body = "ftx=Chin* AND Australia AND invest*"
        headers = {"Content-Type": "application/x-www-form-urlencoded"}
        yield FormRequest(url=search_url,formdata={'ftx': 'Chin* AND Australia AND invest*'},
                          callback=self.parse_search)

    def parse_redirect(self,response):
        #self.i+=1
        redirect_url = 'https://global-factiva-com.virtual.anu.edu.au/ga/default.aspx'
        '''#debug
        filename = 'raw/%d.html' % self.i
        
        with open(filename, 'wb') as f:
            f.write(response.body)
        '''
        key=re.search(r'doLinkPost\(\".*\,\"(.*)\"\,\"(.*)"\)',response.body.decode('utf-8'))
        xformstate = key.group(1)
        xformsesstate = key.group(2)
        yield FormRequest(url=redirect_url,formdata={'_XFORMSTATE':xformstate ,'_XFORMSESSSTATE':xformsesstate},
                          callback=self.parse_article)
    def parse_article(self,response):
        content = response.body.decode('utf-8')
        soup = BeautifulSoup(content,"html.parser")
        
        pdfitem = PDFItem()
        pdfitem['_id'] = soup.find("div","article").get('id')
        pdfitem['Title'] = soup.find("span","enHeadline").getText()
        pdfitem['url'] = 'https://global-factiva-com.virtual.anu.edu.au/pps/default.aspx?pp=PDF&ppstype=Article'
        pdfitem['Xformstate'],_ = self.get_formbody(content)
        pdfitem['SessionDto'] = re.search(r'sessionDto:\'(.*)\',',content).group(1)
        #download pdfitem through filepipeline
        yield pdfitem
        
        articleitems = ArticleItem()
        articleitems['_id'] = soup.find("div","article").get('id')
        articleitems['Title'] = soup.find("span","enHeadline").getText()
        articleitems['Text'] =str(soup.find("div","article"))
        date_str = re.search(r'<b>PD</b>&nbsp;</td><td>(.*)</td></tr>',content).group(1).split('<')[0]
        articleitems['PostDate'] =parse(date_str).strftime('%Y-%m-%d')
        articleitems['CrawlDate'] = dt.today().strftime('%Y-%m-%d')
        articleitems['Class'] = 'UNK'
        
        #logger.info('Start crawing:{}...'.format(articleitems['Title']))
        #print('id:',articleitems['_id'],'title: ',articleitems['Title'])
        yield articleitems
        
        filename = 'articles/%d.html' % self.i
        with open(filename, 'wb') as f:
            f.write(response.body)

    def get_formbody(self,response_body):
        soup = BeautifulSoup(response_body,"html.parser")
        xformstate = None
        xformsesstate = None
        for item in soup.find_all(attrs = {"name": "_XFORMSTATE"}):
            if item.get('value') != '':
                xformstate = item.get('value')
        for iitem in soup.find_all(attrs = {"name": "_XFORMSESSSTATE"}):
            if item.get('value') != '':
                xformsesstate = item.get('value')
        return xformstate,xformsesstate

    def parse_search(self, response):
        '''
        filename = 'logs/search.html'
        with open(filename, 'wb') as f:
            f.write(response.body)
        '''
        
        soup = BeautifulSoup(response.body.decode('utf-8'),"html.parser")
        
        for item in soup.find_all("a","enHeadline"):
            title = item.string
            articles_url='https://global-factiva-com.virtual.anu.edu.au' + item.get('href')[2:]
            #print('Title: {}\nURL: {}\n'.format(title,articles_url))
            yield Request(url=articles_url,callback=self.parse_redirect,dont_filter=True)
        for item in soup.find_all("a","\\\"enHeadline\\\""):
            title = item.string
            articles_url='https://global-factiva-com.virtual.anu.edu.au' + item.get('href')[4:]
            #print('Title: {}\nURL: {}\n'.format(title,articles_url))
            yield Request(url=articles_url,callback=self.parse_redirect,dont_filter=True)
        #find next_page start number
        find = re.search(r'viewNext.*viewNext\(\'(\d*)\'\);',response.body.decode('utf-8'))
        if find != None:
            next_page = find.group(1)
        else:
            next_page = re.search(r'viewNext\(\'(\d*)\'\);',response.body.decode('utf-8')).group(1)

        #find xformstate and xformsesstate
        xformstate,xformsesstate = self.get_formbody(response.body.decode('utf-8'))
        if xformstate and xformsesstate != None:#update session state
            self.xformstate = xformstate
            self.xformsesstate = xformsesstate
        page_url = 'https://global-factiva-com.virtual.anu.edu.au/services/ajaxservice.aspx'
        
       

        #Next page
        self.page +=1
        print('crawl page:{}'.format(self.page))
        yield FormRequest(url=page_url,formdata={'_XFORMSTATE': self.xformstate ,
                  '_XFORMSESSSTATE': self.xformsesstate,
                  'hs': str(self.page * 100),
                  'serviceType': 'factiva.com.ui.services.SearchResultsService'
                  },
                  callback=self.parse_search)
