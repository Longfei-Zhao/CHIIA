# -*- coding: utf-8 -*-

# Define here the models for your scraped items
#
# See documentation in:
# http://doc.scrapy.org/en/latest/topics/items.html

from scrapy import Item, Field


class ArticleItem(Item):
    """ Article structure """
    _id = Field()
    Title = Field()  # wechat_id
    Text = Field()
    PostDate = Field()
    CrawlDate = Field()
    Class = Field()

class PDFItem(Item):
    """ Article structure """
    _id = Field()
    Title = Field()  # wechat_id
    Xformstate = Field()
    SessionDto = Field()

