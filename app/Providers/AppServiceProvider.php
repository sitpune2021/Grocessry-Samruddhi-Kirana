<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use App\Models\Product;
use Illuminate\Support\Facades\Blade;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot()
    {
        Paginator::useBootstrap();
        
        // Fix for MySQL key length issue (utf8mb4)
        Schema::defaultStringLength(191);
        

        Blade::if('canPermission', function ($permission) {

            $user = auth()->user();
 
            return $user && method_exists($user, 'hasPermission')
                && $user->hasPermission($permission);
        });
    }
}
