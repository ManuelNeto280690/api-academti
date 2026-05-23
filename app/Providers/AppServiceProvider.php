<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        \Illuminate\Auth\Notifications\ResetPassword::createUrlUsing(function ($user, string $token) {
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            return $frontendUrl . '/alterar-senha?token=' . $token . '&email=' . urlencode($user->email);
        });
    }
}
