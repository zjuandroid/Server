drop database if exists stockDB;
create database stockDB;
use stockDB;
create table stock_id_tbl
(
id varchar(10) NOT NULL primary key,
name varchar(10) NOT NULL
)CHARSET=utf8;
create table stock_data_tbl
(
	  recordID bigint unsigned primary key not null auto_increment, #股票名称
      code varchar(8) NOT NULL, #股票代码，SZ指在深圳交易的股票
      date varchar(10), #当前显示股票信息的日期
      time varchar(8), #具体时间
      openningPrice float, #今日开盘价
      closingPrice float,  #昨日收盘价
      currentPrice float,  #当前价格
      hPrice float,        #今日最高价
      lPrice float,       #今日最低价
      competitivePrice float, #买一报价
      auctionPrice float,   #卖一报价
      totalNumber int,    #成交的股票数
      turnover float,  #成交额，以元为单位
      increase float,  #涨幅
      buyOne int,      #买一 
      buyOnePrice float, #买一价格
      buyTwo int,  #买二
      buyTwoPrice float, #买二价格
      buyThree int,   #买三
      buyThreePrice float,  #买三价格
      buyFour int,    #买四
      buyFourPrice float, #买四价格
      buyFive int,     #买五
      buyFivePrice float,  #买五价格
      sellOne int,       #卖一
      sellOnePrice float,  #卖一价格
      sellTwo int,      #卖二
      sellTwoPrice float,  #卖二价格
      sellThree int,     #卖三
      sellThreePrice float, #卖三价格
      sellFour int,        #卖四
      sellFourPrice float,  #卖四价格
      sellFive int,       #卖五
      sellFivePrice float,   #卖五价格
	  xqNewPosts int,  #雪球新帖子数
	  xqNewComments int, #雪球新评论数
	  xqTopBuy int, #雪球买入大V数（前100）
	  xqTopSell int, #雪球卖出大V数（前100）
	  xqTotalBuy int, #雪球买入用户数
	  xqTotalSell int #雪球卖出用户数
)CHARSET=utf8;
