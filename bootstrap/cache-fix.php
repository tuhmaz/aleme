<?php

/**
 * Cache Fix Bootstrap for Aleme Educational System
 * This file ensures cache directories exist before Laravel boots
 */

// إنشاء المجلدات المطلوبة
$directories = [
    __DIR__ . '/../storage/framework',
    __DIR__ . '/../storage/framework/cache',
    __DIR__ . '/../storage/framework/cache/data',
    __DIR__ . '/../storage/framework/sessions',
    __DIR__ . '/../storage/framework/views',
    __DIR__ . '/cache',
];

foreach ($directories as $directory) {
    if (!is_dir($directory)) {
        @mkdir($directory, 0755, true);
    }
}

// تعيين متغيرات بيئة افتراضية للـ cache
if (!isset($_ENV['CACHE_DRIVER'])) {
    $_ENV['CACHE_DRIVER'] = 'file';
    putenv('CACHE_DRIVER=file');
}

if (!isset($_ENV['CACHE_STORE'])) {
    $_ENV['CACHE_STORE'] = 'file';
    putenv('CACHE_STORE=file');
}

if (!isset($_ENV['SESSION_DRIVER'])) {
    $_ENV['SESSION_DRIVER'] = 'file';
    putenv('SESSION_DRIVER=file');
}
