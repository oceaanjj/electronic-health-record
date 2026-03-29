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
            return Limit::perMinute(5)->by($request->ip());
        });


        RateLimiter::for('forgot-password', function (Request $request) {
            return Limit::perHour(3)->by($request->input('email') . $request->ip());
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
