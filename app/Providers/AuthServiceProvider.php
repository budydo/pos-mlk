<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any authentication / authorization services.
     */
    public function boot(): void
    {
        // Roles: pemilik, karyawan
        Gate::define('manage-users', fn ($user) => $user->role === 'pemilik');
        Gate::define('manage-products', fn ($user) => $user->role === 'pemilik');
        Gate::define('manage-stock', fn ($user) => $user->role === 'pemilik');
        Gate::define('view-reports', fn ($user) => $user->role === 'pemilik');
        Gate::define('access-dashboard-full', fn ($user) => $user->role === 'pemilik');

        Gate::define('access-cashier', fn ($user) => in_array($user->role, ['karyawan', 'pemilik']));
        Gate::define('create-transactions', fn ($user) => in_array($user->role, ['karyawan', 'pemilik']));
    }
}
