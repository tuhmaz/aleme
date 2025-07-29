<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\CacheManager;

class CacheFixServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // تأكد من تسجيل cache service بشكل صحيح
        $this->app->singleton('cache', function ($app) {
            return new CacheManager($app);
        });

        $this->app->singleton('cache.store', function ($app) {
            return $app['cache']->driver();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // تأكد من وجود مجلدات cache
        $this->ensureCacheDirectoriesExist();
    }

    /**
     * Ensure cache directories exist
     */
    private function ensureCacheDirectoriesExist(): void
    {
        $directories = [
            storage_path('framework/cache'),
            storage_path('framework/cache/data'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
        ];

        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
        }
    }
}
