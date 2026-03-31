<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
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

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinutes(3, 5)->by($request->ip());
        });

        //send reset 6-digit code:
        RateLimiter::for('forgot-password', function (Request $request) {
            return Limit::perMinutes(15, 5)->by($request->input('email') . $request->ip());
        });
        //password reset:
        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perMinutes(3, 5)->by($request->ip());
        });

        Gate::define('is-admin', function (User $user) {
            return strtolower((string) $user->role) === 'admin';
        });

        // Define the Gate for doctor access (for consistency)
        Gate::define('is-doctor', function (User $user) {
            return strtolower((string) $user->role) === 'doctor';
        });

        // Define the Gate for nurse access
        Gate::define('is-nurse', function (User $user) {
            return strtolower((string) $user->role) === 'nurse';
        });

        //AUTO LOGIN
        // if (app()->environment('local') && !Auth::check()) {
        //     //  Replace 3 with the ID of any user you want to use for testing
        //     // 1 = Admin
        //     // 2 = Doctor
        //     // 3 = Nurse
        //     Auth::loginUsingId(3);
        // }

    }
}
