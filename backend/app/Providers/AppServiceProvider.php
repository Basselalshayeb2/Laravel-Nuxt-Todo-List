<?php

namespace App\Providers;

use App\Support\ApiResponse;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Response;
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

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Response::macro('success', fn (mixed $data = null, ?string $message = null, int $status = 200, array $meta = []) => ApiResponse::success($data, $message, $status, $meta));
        Response::macro('error', fn (string $message, string $code, int $status, array $errors = []) => ApiResponse::error($message, $code, $status, $errors));

        RateLimiter::for('login', fn (Request $request): Limit => Limit::perMinute(20)->by($request->ip()));
    }
}
