<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\Customer;

class AppServiceProvider extends ServiceProvider
{
    
    public function register(): void
    {
        $this->app->bind('customer', function($app) {
            return new Customer();
        });
    }
    public function boot()
    {
    Paginator::useBootstrap();
    }
}