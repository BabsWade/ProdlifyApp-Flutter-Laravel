<?php

namespace App\Providers;
use Illuminate\Support\Facades\Gate;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        //
        Gate::define('is-admin', function ($user) {
            return $user->hasRole('admin');
        });
        
    }
}
