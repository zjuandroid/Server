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
	  recordID bigint unsigned primary key not null auto_increment, #��Ʊ����
      code varchar(8) NOT NULL, #��Ʊ���룬SZָ�����ڽ��׵Ĺ�Ʊ
      date varchar(10), #��ǰ��ʾ��Ʊ��Ϣ������
      time varchar(8), #����ʱ��
      openningPrice float, #���տ��̼�
      closingPrice float,  #�������̼�
      currentPrice float,  #��ǰ�۸�
      hPrice float,        #������߼�
      lPrice float,       #������ͼ�
      competitivePrice float, #��һ����
      auctionPrice float,   #��һ����
      totalNumber int,    #�ɽ��Ĺ�Ʊ��
      turnover float,  #�ɽ����ԪΪ��λ
      increase float,  #�Ƿ�
      buyOne int,      #��һ 
      buyOnePrice float, #��һ�۸�
      buyTwo int,  #���
      buyTwoPrice float, #����۸�
      buyThree int,   #����
      buyThreePrice float,  #�����۸�
      buyFour int,    #����
      buyFourPrice float, #���ļ۸�
      buyFive int,     #����
      buyFivePrice float,  #����۸�
      sellOne int,       #��һ
      sellOnePrice float,  #��һ�۸�
      sellTwo int,      #����
      sellTwoPrice float,  #�����۸�
      sellThree int,     #����
      sellThreePrice float, #�����۸�
      sellFour int,        #����
      sellFourPrice float,  #���ļ۸�
      sellFive int,       #����
      sellFivePrice float,   #����۸�
	  xqNewPosts int,  #ѩ����������
	  xqNewComments int, #ѩ����������
	  xqTopBuy int, #ѩ�������V����ǰ100��
	  xqTopSell int, #ѩ��������V����ǰ100��
	  xqTotalBuy int, #ѩ�������û���
	  xqTotalSell int #ѩ�������û���
)CHARSET=utf8;