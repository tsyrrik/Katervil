<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            StatefulGuard::class,
            static function (): StatefulGuard {
                $guard = Auth::guard('web');

                if (!$guard instanceof StatefulGuard) {
                    throw new \RuntimeException('Web guard must be stateful.');
                }

                return $guard;
            }
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {}
}
