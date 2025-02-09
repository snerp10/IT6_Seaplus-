<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Customer;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Order::class => OrderPolicy::class,
    ];
    
    public function register(): void
    {
        $this->app->bind('customer', function($app) {
            return new Customer();
        });
    }
}