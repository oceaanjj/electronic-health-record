<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var arraya
     */
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('access-admin-page', function (User $user) {
            return $user->role === 'Admin';
        });

        // Define the Gate for doctor access (for consistency)
        Gate::define('access-doctor-page', function (User $user) {
            return $user->role === 'Doctor';
        });

        // Define the Gate for nurse access
        Gate::define('is-nurse', function (User $user) {
            return $user->role === 'Nurse';
        });


    }
}