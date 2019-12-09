<?php
/**
 * Created by PhpStorm.
 * User: danielshen
 * Date: 2019/8/21
 * Time: 16:04
 */

require(dirname(__DIR__) . '/vendor/autoload.php');

$config = [
    'default' => [
        // 网关调用策略，默认：顺序调用
        'strategy' => \Shencongcong\Ldap\Strategies\OrderStrategy::class,
        // 默认可用的发送网关
        'gateways' => [
            'home','idc'
        ],
    ],
    'gateways' => [
        'home' => [
            'url' => 'LDAP://10.0.127.110/',
        ],
        'idc' => [
            'url' => 'LDAP://10.1.4.150/',
        ],
    ],
];
$ldap = new Shencongcong\Ldap\Ldap($config);
/*$password = '$Aa431275';
$gateways = ['home1','idc1'];*/
$email = 'dan';
$res = $ldap->emailSearch($email);