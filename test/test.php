<?php
/**
 * Created by PhpStorm.
 * User: danielshen
 * Date: 2019/8/13
 * Time: 16:58
 */

/*require_once './vendor/autoload.php';

$config = [
    'host' => '10.0.150.72',
    'database' => 'test',
    'user' => 'root',
    'password' => '123u123U.sa',
    'port' => '3306',
];

$db = \Shencongcong\Mysql\Mysql::getDbInstance($config);
$res = $db->table('test')->select();
var_dump($res);*/
$gateways = ['ali'=>1,'ten'=>2,'baidu'=>3];
//var_dump(array_keys($gateways));exit;
//var_dump(mt_rand()  - mt_rand());exit;
uasort($gateways,function(){
    return mt_rand() - mt_rand();
});
var_dump($gateways);exit;
var_dump(array_keys($gateways));exit;
//return array_keys($gateways);