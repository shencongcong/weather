<?php
/**
 * Created by PhpStorm.
 * User: danielshen
 * Date: 2019/8/17
 * Time: 16:13
 */


$res = shmcache('name','hlcc',600);
//var_dump($res);exit;
var_dump(shmcache('name'));

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