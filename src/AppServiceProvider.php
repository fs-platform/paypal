<?php

namespace Smbear\Paypal;

use Illuminate\Support\ServiceProvider;
use Smbear\Paypal\Services\PaypalService;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('paypal',function (){
            return new PaypalService();
        });

        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'paypal');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('paypal.php'),
            ]);
        }
    }
}