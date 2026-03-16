<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;

class AppServiceProvider extends ServiceProvider
{
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
        // Kode reset password Anda yang sudah ada
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        /** * SOLUSI PENTEST: Definisi Rate Limiter untuk Login
         * Membatasi 5 percobaan per menit berdasarkan IP Address
         */
        RateLimiter::for('login', function (Request $request) {
            $key = ($request->input('npk') ?: $request->input('username') ?: $request->ip());

            return Limit::perMinute(5)->by($key)->response(function (Request $request, array $headers) use ($key) {
                // Gunakan header Retry-After yang diumpankan Laravel secara otomatis
                $seconds = $headers['Retry-After'] ?? 60;

                if (!$request->expectsJson()) {
                    return back()
                        ->withInput()
                        ->withErrors(['login' => 'Terlalu banyak percobaan login.'])
                        ->with('lockout_seconds', $seconds); // Kirim detik ke session
                }

                return response()->json(['message' => 'Terlalu banyak percobaan login.', 'retry_after' => $seconds], 429, $headers);
            });
        });
    }
}
