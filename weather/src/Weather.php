<?php
/**
 * Created by PhpStorm.
 * User: danielshen
 * Date: 2019/7/5
 * Time: 17:33
 */

namespace Shencongcong\Weather;
use GuzzleHttp\Client;

class Weather
{
    protected $key;

    protected $guzzleOptions = [];

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
    }



}