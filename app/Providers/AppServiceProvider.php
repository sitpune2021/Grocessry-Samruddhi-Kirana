<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use App\Models\Product;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot()
    {
        Blade::if('canPermission', function ($permission) {

            $user = auth()->user();
 
            return $user && method_exists($user, 'hasPermission')
                && $user->hasPermission($permission);
        });
    }
}
