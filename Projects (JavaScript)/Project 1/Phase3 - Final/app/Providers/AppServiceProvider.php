<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        Blade::if('admin', function () {            
            if (auth()->user() && auth()->user()->is_admin) {
                return 1;
            }
            return 0;
        });

        Blade::if('notadmin', function () {            
            if (auth()->user() && auth()->user()->is_admin) {
                return 0;
            }
            return 1;
        });
    }
}