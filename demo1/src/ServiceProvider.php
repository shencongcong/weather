<?php

namespace Shencongcong\Demo1;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

    public function boot()
    {
        //
    }

    public function register()
    {
        $this->app->singleton('demo1', function () {
            return new Demo1;
        });
    }

}