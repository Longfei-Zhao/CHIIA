# -*- coding: utf-8 -*-

# Define your item pipelines here
#
# Don't forget to add your pipeline to the ITEM_PIPELINES setting
# See: http://doc.scrapy.org/en/latest/topics/item-pipeline.html
import pymongo
from CHIIA.items import ArticleItem


class MongoDBPipleline(object):
    def __init__(self):
        client = pymongo.MongoClient("localhost", 27017)
        db = client["CHIIA"]
        self.Articles = db["Articles"]
    
    
    def process_item(self, item, spider):
        """ 判断item的类型，并作相应的处理，再入数据库 """
        
        if isinstance(item, ArticleItem):
            try:
                self.Articles.insert(dict(item))
            except Exception:
                pass

        return item
'''
class PDFPipeline(FilesPipeline):
    def get_pdf_requests(self,item,info):
        for url in item[

'''
