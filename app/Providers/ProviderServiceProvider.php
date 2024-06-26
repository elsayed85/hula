<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Core\Contracts\ProviderContract;

class ProviderServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('ProviderFactory', function ($app) {
            return new \App\Core\Providers\ProviderFactory($app);
        });
    }
}
