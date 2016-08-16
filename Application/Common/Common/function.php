<?php
use QL\Snoopy;
use QL\QueryList;
//import('Common.Tool.phpQuery');

function startCollect1() {
    $snoopy=new Snoopy();
    $snoopy->agent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36";
    $snoopy->referer = "http://www.ivsky.com/tupian/geguo_guoqi_v1773/";
    $post['telephone'] ='13186978264';//根据你要模拟登陆的网站具体的传值 名称来定
    $post['password'] ='wc63650312';//根据你要模拟登陆的网站具体的传值 名称来定
    $url='http://xueqiu.com/user/login';//登陆数据提交的URL地址
    $snoopy->submit($url,$post);
    $url = 'http://xueqiu.com/cubes/discover/rank/cube/list.json?market=cn&sale_flag=0&stock_positions=0&sort=best_benefit&category=12&profit=monthly_gain&page=1&count=20';
//    $url = 'http://xueqiu.com/statuses/search.json?count=10&comment=0&symbol=SZ002312&hl=0&source=user&sort=time&page=1';
    $snoopy->fetch($url);//希望获取的页面数据
//    echo $snoopy->results;//输出结果，登陆成功
}

function startCollect2(){
    dump('kkkkkkkkkkkkkkk');
    $ch = curl_init();
    $url = 'http://apis.baidu.com/apistore/stockservice/stock?stockid=sz002230,sh600165&list=1';
    $header = array(
        'apikey: 268891028d6ea0920c751861b698ea53',
    );
    // 添加apikey到header
    curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // 执行HTTP请求
    curl_setopt($ch , CURLOPT_URL , $url);
    $res = curl_exec($ch);

    print($res);

//    dump(json_decode($res));
}

function startCollect3() {
    $snoopy=new Snoopy();
    $snoopy->agent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36";
    $snoopy->referer = "http://www.ivsky.com/tupian/geguo_guoqi_v1773/";
//    $url = 'http://yunhq.sse.com.cn:32041/v1/sh1/list/exchange/equity?callback=jQuery1112011978950281627476_1459779335597&select=code%2Cname%2Copen%2Chigh%2Clow%2Clast%2Cprev_close%2Cchg_rate%2Cvolume%2Camount%2Ctradephase%2Cchange%2Camp_rate&order=&begin=1&end=2000&_=1459779335604';
//    $url = 'http://xueqiu.com/statuses/search.json?count=10&comment=0&symbol=SZ002312&hl=0&source=user&sort=time&page=1';
    $url = 'http://quote.eastmoney.com/stocklist.html';
    $snoopy->fetch($url);//希望获取的页面数据
    echo $snoopy->results;
}

function startCollect() {
    $snoopy=new Snoopy();
    $snoopy->agent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36";
    $snoopy->referer = "https://xueqiu.com/user/login";
    $post['telephone'] ='13186978264';//根据你要模拟登陆的网站具体的传值 名称来定
    $post['password'] ='wc63650312';//根据你要模拟登陆的网站具体的传值 名称来定
    $url='https://xueqiu.com/user/login';//登陆数据提交的URL地址
    $flag = $snoopy->submit($url,$post);
//    $url = 'http://xueqiu.com/cubes/discover/rank/cube/list.json?market=cn&sale_flag=0&stock_positions=0&sort=best_benefit&category=12&profit=monthly_gain&page=1&count=20';
//    $url = 'http://xueqiu.com/statuses/search.json?count=10&comment=0&symbol=SZ002312&hl=0&source=user&sort=time&page=1';
//    $snoopy->fetch($url);

    $stockList = M("stock_id_tbl")->limit(0,10)->select();
    $dao = M("stock_data_tbl");
    $time = strtotime('yesterday')*1000;
    $pageCount = 10;
    foreach($stockList as $stock) {
        $code = $stock['stock_type'].$stock['stock_id'];
        $num = 0;

        for($i = 1; ;$i++) {
            $url = 'https://xueqiu.com/statuses/search.json?count='.$pageCount.'&comment=0&symbol='.$code.'&hl=0&source=user&sort=time&page='.$i;
            $flag = $snoopy->fetch($url);
            $json = json_decode($snoopy->results);
            $jsonList = $json->list;

//            dump($jsonList[$pageCount-1]->created_at);
//            dump($time);

            if($jsonList[count($jsonList)-1]->created_at > $time) {
                $num += count($jsonList);
//                dump($num);
                continue;
            }
            else {
                for($j = 0; $j < count($jsonList); $j++) {
                    if($jsonList[$j]->created_at > $time) {
                        $num++;
//                        dump($num);
                    }
                    else {
                        break 2;
                    }
                }
            }
        }
        $condition['STOCK_CODE'] = $data['STOCK_CODE'] = $code;
        $condition['CURRENT_DATE_INFO'] = $data['CURRENT_DATE_INFO'] = date("Y-m-d",strtotime("-1 day"));
        $data['XQ_NEW_POSTS'] = $num;
        if($dao->where($condition)->find()) {
            $dao->where($condition)->save($data);
        }
        else {
            $dao->add($data);
        }


    }
//    $snoopy->fetch('https://xueqiu.com/statuses/search.json?count=20&comment=0&symbol=SZ002312&hl=0&source=user&sort=time&page=1');
//    print_r($snoopy->results);
//    $json = json_decode($snoopy->results);
//    print_r(sizeof($json->list));

}

function startCreateDB() {
    $url = 'http://quote.eastmoney.com/stocklist.html';
    $html = getPage($url);
//    echo $html;
    $rules = array(
//        'sh' => array('.quotebody a', 'href')
//        'sh' => array('ul:first li a', 'text')
        'sh' => array('ul:first', 'text'),
        'sz' => array('ul:last li', 'text')
    );
    $range = "#quotesearch";
//    $ql = QueryList::Query($html, $rules,$range, 'gb2312', 'gb2312');
    $ql = QueryList::Query($html, $rules,$range, 'utf-8', 'gb2312');
//    print_r($ql->data);

//    $shStocklist = $ql->data[0][sh];
//    $shStocklist = explode(')', $ql->data[0][sh]);
//    $i = 0;
//    foreach($shStocklist as $stock) {
//        $items = explode('(', $stock);
//        $shStocks[$i][name] = trim($items[0]);
//        $shStocks[$i][code] = trim($items[1]);
//        $i++;
//    }
////    print_r($shStocklist);
//    print_r($shStocks);

    $shStocks = getStockArray($ql->data[0][sh]);
    print_r($shStocks);
    $szStocks = getStockArray($ql->data[0][sz]);
    print_r($szStocks);
    $dao = M("stock_id_tbl");
    fillDB($dao, $shStocks, 'sh');
    fillDB($dao, $szStocks, 'sz');
}

function getPage($url) {
    $snoopy=new Snoopy();
    $snoopy->agent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36";
    $snoopy->referer = "http://www.ivsky.com/tupian/geguo_guoqi_v1773/";
    $snoopy->fetch($url);//希望获取的页面数据
    return $snoopy->results;
}

function fillDB($dao, $list, $type) {
    foreach($list as $stock) {
        $data[STOCK_ID] = $stock[code];
        $data[STOCK_NAME] = $stock[name];
        $data[STOCK_TYPE] = $type;
        $dao->add($data);
    }
}

function getStockArray($string) {
    $stocklist = explode(')', $string);
    $i = 0;
    foreach($stocklist as $stock) {
        if(empty($stock)) continue;
        $items = explode('(', $stock);
        $stocks[$i][name] = trim($items[0]);
        $stocks[$i][code] = trim($items[1]);
        $i++;
    }
    return $stocks;
}

function startCreateDB1()
{
    $snoopy=new Snoopy();
    $snoopy->agent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36";
    $snoopy->referer = "http://www.ivsky.com/tupian/geguo_guoqi_v1773/";
    $url = 'http://job.blueidea.com';
    $snoopy->fetch($url);//希望获取的页面数据
    $html = $snoopy->results;
    phpQuery::newDocumentFile($html);
    $companies = pq('#hotcoms .coms')->find('div');
    foreach($companies as $company)
    {
        echo pq($company)->find('h3 a')->text()."<br>";
    }
}