<?php
/**
 * Created by PhpStorm.
 * User: danielshen
 * Date: 2019/8/15
 * Time: 15:55
 */


$curl = 'https://www.baidu.com/api/v1';
_httpPost($curl);

function _httpPost($url = "", $requestData = [])
{
    $tempu=parse_url($url);
    $domain=$tempu['host'];
    $redis = new redis();
    $redis->connect('10.0.150.72', '6379') || die("连接失败！");
    $redis->auth("123u123u.sa"); //授权
    $guid = guid();
    $redis->set($domain.':'.$guid,$url,600);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT,30);
    //普通数据
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($requestData));
    $res = curl_exec($curl);
/*    if($res !== false){
        $redis->delete($guid);
    }*/

    curl_close($curl);
    $res = json_decode($res, 1);
    return $res;
}

function guid(){
    if (function_exists(‘com_create_guid’)){
        return com_create_guid();
    }else{
        mt_srand((double)microtime()*10000);// optional for php 4.2.0 and up.
        echo(mt_rand());
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// “-”
        $uuid = chr(123)// “{”
            .substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12)
            .chr(125);// “}”
        return $uuid;
    }
}

