<?php
/**
 * Created by PhpStorm.
 * User: danielshen
 * Date: 2019/7/15
 * Time: 15:50
 */

namespace Shencongcong\Ldap;


class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

    public function boot()
    {

    }

    public function register()
    {
        // 发布配置文件
        $this->publishes([__DIR__ . '/config/ldap.php' => config_path('ldap.php'),'config']);

        $this->app->singleton(Ldap::class,function (){
            return Ldap::getInstance();
        });
    }
}