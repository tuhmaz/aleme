<?php

/**
 * Safe Package Discovery Script for Aleme Educational System
 * This script ensures cache service is available before running package discovery
 */

// تأكد من وجود ملف artisan
if (!file_exists(__DIR__ . '/../artisan')) {
    echo "Artisan file not found. Skipping package discovery.\n";
    exit(0);
}

// تأكد من وجود ملف .env
if (!file_exists(__DIR__ . '/../.env')) {
    if (file_exists(__DIR__ . '/../.env.example')) {
        copy(__DIR__ . '/../.env.example', __DIR__ . '/../.env');
        echo "Created .env file from .env.example\n";
    } else {
        echo "No .env file found. Skipping package discovery.\n";
        exit(0);
    }
}

// تحقق من وجود APP_KEY وإنشاؤه إذا لم يكن موجود
$envContent = file_get_contents(__DIR__ . '/../.env');
if (strpos($envContent, 'APP_KEY=') === false || preg_match('/APP_KEY=\s*$/', $envContent) || preg_match('/APP_KEY=""/', $envContent)) {
    echo "Generating application key...\n";
    $command = 'cd ' . escapeshellarg(__DIR__ . '/..') . ' && php artisan key:generate --force';
    exec($command . ' 2>&1', $keyOutput, $keyExitCode);
    if ($keyExitCode === 0) {
        echo "Application key generated successfully.\n";
    } else {
        echo "Failed to generate application key, but continuing...\n";
    }
}

// إنشاء المجلدات المطلوبة
$directories = [
    __DIR__ . '/../storage/framework/cache',
    __DIR__ . '/../storage/framework/cache/data',
    __DIR__ . '/../storage/framework/sessions',
    __DIR__ . '/../storage/framework/views',
    __DIR__ . '/../bootstrap/cache',
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "Created directory: $dir\n";
    }
}

// تشغيل package discovery مع معالجة الأخطاء
$command = 'cd ' . escapeshellarg(__DIR__ . '/..') . ' && php artisan package:discover --ansi';
$output = [];
$exitCode = 0;

exec($command . ' 2>&1', $output, $exitCode);

if ($exitCode === 0) {
    echo "Package discovery completed successfully.\n";
    foreach ($output as $line) {
        echo $line . "\n";
    }
} else {
    echo "Package discovery failed, but continuing...\n";
    echo "Error output:\n";
    foreach ($output as $line) {
        echo $line . "\n";
    }
    
    // محاولة إصلاح المشكلة
    echo "Attempting to fix cache issues...\n";
    
    // مسح ملفات cache
    $cacheFiles = glob(__DIR__ . '/../bootstrap/cache/*.php');
    foreach ($cacheFiles as $file) {
        if (basename($file) !== '.gitignore') {
            unlink($file);
        }
    }
    
    // محاولة مرة أخرى
    exec($command . ' 2>&1', $output2, $exitCode2);
    if ($exitCode2 === 0) {
        echo "Package discovery succeeded after cache fix.\n";
    } else {
        echo "Package discovery still failing, but installation can continue.\n";
    }
}

exit(0);
?>
