<?php
/**
 * Created by PhpStorm.
 * User: danielshen
 * Date: 2019/8/17
 * Time: 11:10
 */

$curl = 'http://hlcc.123u.com/mysql.php';
$res = _httpPost($curl);
var_dump($res);

function _httpPost($url = "", $requestData = [])
{



    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT,15);
    //普通数据
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($requestData));
    $res = huanle_curl_exec($curl);


    curl_close($curl);
    return $res;
}

function huanle_curl_exec($curl){
    $tempu=parse_url($curl);
    $domain=$tempu['host'];

    $times = shmcache($domain);
    if($times > 20){
        // 上报
        return  $curl.'curl阻塞请及时处理';
    }
    shmcache($domain,$times+1,60);

    $res = curl_exec($curl);

    $times = shmcache($domain);
    shmcache($domain,$times-1,60);

    return $res;
}

function guid(){
    if (function_exists(‘com_create_guid’)){
        return com_create_guid();
    }else{
        mt_srand((double)microtime()*10000);// optional for php 4.2.0 and up.
        //  echo(mt_rand());
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// “-”
        $uuid = chr(123)
            .substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12)
            .chr(125);
        return $uuid;
    }
}


function shmcache($key, $val=null, $expire=100) {
    static $_shm = null;
    if ( null === $_shm ) $_shm = @shm_attach(crc32(config('mcache.solt', null, 'mcac  he.solt')),
        config('cache.size', null, 10485760), 0755);
    if (($time = time()) && ($k = crc32($key)) && $val && $expire){
        shm_put_var($_shm, $k, array($time + $expire, $val));
        return $val;
    }
    return shm_has_var($_shm, $k) && ($data = shm_get_var($_shm, $k)) && $data[0] >   $time ? $data[1] : null;
}

function config($key = null, $value = null, $default=null) {
    static $_config = array();
    // get all config
    if ($key === null) return $_config;
    // if key is source, load ini file and return
    if ($key === 'source' && file_exists($value)) return $_config = array_merge($_config, parse_ini_file($value, true));
    // for all other string keys, set or get
    if (is_string($key)) {
        if ($value === null)
            return (isset($_config[$key]) ? $_config[$key] : $default);
        return ($_config[$key] = $value);
    }
    // setting multiple settings
    if (is_array($key) && array_diff_key($key, array_keys(array_keys($key))))
        $_config = array_merge($_config, $key);
}