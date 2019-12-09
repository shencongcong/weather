<?php
/**
 * Created by PhpStorm.
 * User: danielshen
 * Date: 2019/8/19
 * Time: 14:22
 */

namespace Shencongcong\HuanleCurl;

use Shencongcong\HuanleCurl\Exceptions\InvalidArgumentException;

class HuanleCurl
{

    private $times = 20;

    private $expire = 60;

    private $cacheKey = 'mcache.solt';

    private $cacheRam = 10485760;

    private static $instance;

    const NOTICE_PREFIX = 'notice_';

    private function __construct($config)
    {
        $this->times = isset($config['times']) ? $config['times'] : $this->times;
        $this->expire = isset($config['expire']) ? $config['expire'] : $this->expire;
        $this->cacheKey = isset($config['cacheKey ']) ? $config['cacheKey '] : $this->cacheKey;
        $this->cacheRam = isset($config['cacheRam']) ? $config['cacheRam'] : $this->cacheRam;
    }

    public static function getInstance($config)
    {
        if ( !(self::$instance instanceof self) ) {
            self::$instance = new HuanleCurl($config);
        }

        return self::$instance;
    }

    public function huanle_curl_exec(&$curl, $url)
    {
        if ( !isset(parse_url($url)['host']) ) {
            throw new InvalidArgumentException('url格式错误');
        }
        $domain = parse_url($url)['host'];

        if ( $this->mcache($domain) != null ) {
            $times = $this->mcache($domain);
        }
        else {
            $times = 0;
        }
        if ( $times > $this->times ) {
            // 上报、通知
            if(empty($this->mcache('notice'.$domain))){
                $reportParam = [
                    'title'=>'curl阻塞报警',
                    'description'=> $url.'请求阻塞请及时排查',
                    'url' => $url,
                    'sender' =>['danielshen'],
                ];
                $reportUrl = 'https://tc-notice.123u.com/workWechat/sendText';
                $this->_httpPost($reportUrl,$reportParam);
                $this->mcache('notice'.$domain,1,60);
            }


            return 'curl阻塞请及时处理,阻塞的连接:' . $url;
        }
        $this->mcache($domain, $times + 1, 60);
        $res = curl_exec($curl);
        $times = $this->mcache($domain);
        if ( $res != false ) {
            $this->mcache($domain, $times - 1, 60);
        }
        return $res;
    }

    function _httpPost($url = "", $requestData = [])
    {

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        //普通数据
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($requestData));
        $res = curl_exec($curl);

        curl_close($curl);

        $res = json_decode($res, 1);
        return $res;
    }

    public function mcache($key, $val = null, $expire = 100)
    {
        /**
         * 说明:
         * crc32 函数 计算一个字符串的 crc32 多项式
         * 10485760字节 = 10M
         * 安装 shmop扩展
         *
         */
        static $_caches = null;
        static $_shm = null;
        if ( null === $_shm ) {
            $_shm = @shmop_open(crc32($this->cacheKey),
                'c', 0755, $this->cacheRam);
        }
        if ( null === $_caches && $_shm && ($size = intval(shmop_read($_shm, 0, 10))) ) {
            $_caches = $size ? @unserialize(@shmop_read($_shm, 10, $size)) : [];
        }
        // 设置缓存
        if ( ($time = time()) && $val && $expire ) {
            $_caches[$key] = [$time + intval($expire), $val];
            //var_dump(serialize($_caches));
            if ( $_shm && ($size = @shmop_write($_shm, serialize(array_filter($_caches, function ($n) use ($time) {
                    return $n[0] > $time;
                })), 10)) ) {
                @shmop_write($_shm, sprintf('%10d', $size), 0);
            }
            return $val;
        }
        // 读取缓存
        return (isset($_caches[$key]) && $_caches[$key][0] > $time) ? $_caches[$key][1] : null;
    }

}
