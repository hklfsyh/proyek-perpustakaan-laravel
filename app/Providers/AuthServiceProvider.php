<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Aturan 1: 'manage-users' -> hanya user dengan role 'admin' yang lolos.
        Gate::define('manage-users', function ($user) {
            return $user->role == 'admin';
        });

        // Aturan 2: 'manage-library' -> hanya user dengan role 'admin' atau 'librarian' yang lolos.
        Gate::define('manage-library', function ($user) {
            return in_array($user->role, ['admin', 'librarian']);
        });
    }
}
