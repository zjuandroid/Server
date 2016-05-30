drop database if exists stockDB;
create database stockDB;
use stockDB;
create table stock_id_tbl
(
STOCK_ID varchar(10) NOT NULL primary key,
STOCK_NAME varchar(10) NOT NULL,
STOCK_TYPE varchar(2) NOT NULL
)CHARSET=utf8;
create table stock_data_tbl
(
	  RECORD_ID bigint unsigned primary key not null auto_increment, #股票名称
      STOCK_CODE varchar(8) NOT NULL, #股票代码，SZ指在深圳交易的股票
      CURRENT_DATE_INFO varchar(10), #当前显示股票信息的日期
      CURRENT_TIME_INFO varchar(8), #具体时间
      OPEN_PRICE float, #今日开盘价
      CLOSE_PRICE float,  #昨日收盘价
      CURRENT_PRICE float,  #当前价格
      HIGH_PRICE float,        #今日最高价
      LOW_PRICE float,       #今日最低价
      COMPETITIVE_PRICE float, #买一报价
      ACTION_PRICE float,   #卖一报价
      TOTAL_NUMBER int,    #成交的股票数
      TURN_OVER float,  #成交额，以元为单位
      INCREASE float,  #涨幅
      BUY_ONE int,      #买一 
      BUY_ONE_PRICE float, #买一价格
      BUY_TWO int,  #买二
      BUY_TWO_PRICE float, #买二价格
      BUY_THREE int,   #买三
      BUY_THREE_PRICE float,  #买三价格
      BUY_FOUR int,    #买四
      BUY_FOUR_PRICE float, #买四价格
      BUY_FIVE int,     #买五
      BUY_FIVE_PRICE float,  #买五价格
      SELL_ONE int,       #卖一
      SELL_ONE_PRICE float,  #卖一价格
      SELL_TWO int,      #卖二
      SELL_TWO_PRICE float,  #卖二价格
      SELL_THREE int,     #卖三
      SELL_THREE_PRICE float, #卖三价格
      SELL_FOUR int,        #卖四
      SELL_FOUR_PRICE float,  #卖四价格
      SELL_FIVE int,       #卖五
      SELL_FIVE_PRICE float,   #卖五价格
	  XQ_NEW_POSTS int,  #雪球新帖子数
	  XQ_NEW_COMMENTS int, #雪球新评论数
	  XQ_TOP_BUY int, #雪球买入大V数（前100）
	  XQ_TOP_SELL int, #雪球卖出大V数（前100）
	  XQ_TOTAL_BUY int, #雪球买入用户数
	  XQ_TOTAL_SELL int #雪球卖出用户数
)CHARSET=utf8;