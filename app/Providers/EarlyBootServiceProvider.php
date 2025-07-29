<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\CacheManager;
use Illuminate\Cache\Repository;

class EarlyBootServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     * This provider runs very early to ensure cache is available
     */
    public function register(): void
    {
        // تسجيل cache service مبكراً
        $this->app->singleton('cache', function ($app) {
            return new CacheManager($app);
        });

        $this->app->singleton('cache.store', function ($app) {
            return $app['cache']->driver();
        });

        // تسجيل cache repository
        $this->app->bind(Repository::class, function ($app) {
            return $app['cache.store'];
        });

        // إنشاء المجلدات المطلوبة
        $this->ensureDirectoriesExist();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // لا شيء هنا - كل شيء في register
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return ['cache', 'cache.store'];
    }

    /**
     * Ensure required directories exist
     */
    private function ensureDirectoriesExist(): void
    {
        $directories = [
            storage_path('framework'),
            storage_path('framework/cache'),
            storage_path('framework/cache/data'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            base_path('bootstrap/cache'),
        ];

        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                @mkdir($directory, 0755, true);
            }
        }
    }
}
