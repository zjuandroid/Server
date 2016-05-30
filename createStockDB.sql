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
	  RECORD_ID bigint unsigned primary key not null auto_increment, #��Ʊ����
      STOCK_CODE varchar(8) NOT NULL, #��Ʊ���룬SZָ�����ڽ��׵Ĺ�Ʊ
      CURRENT_DATE_INFO varchar(10), #��ǰ��ʾ��Ʊ��Ϣ������
      CURRENT_TIME_INFO varchar(8), #����ʱ��
      OPEN_PRICE float, #���տ��̼�
      CLOSE_PRICE float,  #�������̼�
      CURRENT_PRICE float,  #��ǰ�۸�
      HIGH_PRICE float,        #������߼�
      LOW_PRICE float,       #������ͼ�
      COMPETITIVE_PRICE float, #��һ����
      ACTION_PRICE float,   #��һ����
      TOTAL_NUMBER int,    #�ɽ��Ĺ�Ʊ��
      TURN_OVER float,  #�ɽ����ԪΪ��λ
      INCREASE float,  #�Ƿ�
      BUY_ONE int,      #��һ 
      BUY_ONE_PRICE float, #��һ�۸�
      BUY_TWO int,  #���
      BUY_TWO_PRICE float, #����۸�
      BUY_THREE int,   #����
      BUY_THREE_PRICE float,  #�����۸�
      BUY_FOUR int,    #����
      BUY_FOUR_PRICE float, #���ļ۸�
      BUY_FIVE int,     #����
      BUY_FIVE_PRICE float,  #����۸�
      SELL_ONE int,       #��һ
      SELL_ONE_PRICE float,  #��һ�۸�
      SELL_TWO int,      #����
      SELL_TWO_PRICE float,  #�����۸�
      SELL_THREE int,     #����
      SELL_THREE_PRICE float, #�����۸�
      SELL_FOUR int,        #����
      SELL_FOUR_PRICE float,  #���ļ۸�
      SELL_FIVE int,       #����
      SELL_FIVE_PRICE float,   #����۸�
	  XQ_NEW_POSTS int,  #ѩ����������
	  XQ_NEW_COMMENTS int, #ѩ����������
	  XQ_TOP_BUY int, #ѩ�������V����ǰ100��
	  XQ_TOP_SELL int, #ѩ��������V����ǰ100��
	  XQ_TOTAL_BUY int, #ѩ�������û���
	  XQ_TOTAL_SELL int #ѩ�������û���
)CHARSET=utf8;