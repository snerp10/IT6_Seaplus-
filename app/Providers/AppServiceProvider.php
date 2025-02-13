<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Customer;

class AppServiceProvider extends ServiceProvider
{
    
    public function register(): void
    {
        $this->app->bind('customer', function($app) {
            return new Customer();
        });
    }
}