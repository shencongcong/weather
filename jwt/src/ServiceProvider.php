<?php
/**
 * Created by PhpStorm.
 * User: danielshen
 * Date: 2019/7/15
 * Time: 15:50
 */

namespace Shencongcong\Jwt;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

    public function boot()
    {

    }

    public function register()
    {
        $this->app->singleton(Jwt::class,function (){
            return Jwt::getInstance();
        });

    }
}