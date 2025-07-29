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

// تحقق من وجود .env وإنشاؤه إذا لزم الأمر
if (!file_exists(__DIR__ . '/../.env') && file_exists(__DIR__ . '/../.env.example')) {
    copy(__DIR__ . '/../.env.example', __DIR__ . '/../.env');
}

// تعيين APP_KEY افتراضي إذا لم يكن موجود
if (!isset($_ENV['APP_KEY']) || empty($_ENV['APP_KEY'])) {
    // إنشاء مفتاح مؤقت لتجنب الخطأ
    $tempKey = 'base64:' . base64_encode(random_bytes(32));
    $_ENV['APP_KEY'] = $tempKey;
    putenv('APP_KEY=' . $tempKey);
}
