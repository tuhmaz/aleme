<?php

/**
 * Generate Application Key Script for Aleme Educational System
 * This script ensures APP_KEY is generated before Laravel configuration
 */

$envFile = __DIR__ . '/../.env';
$envExampleFile = __DIR__ . '/../.env.example';

// تأكد من وجود ملف .env
if (!file_exists($envFile)) {
    if (file_exists($envExampleFile)) {
        copy($envExampleFile, $envFile);
        echo "Created .env file from .env.example\n";
    } else {
        echo "Error: No .env.example file found!\n";
        exit(1);
    }
}

// قراءة محتوى ملف .env
$envContent = file_get_contents($envFile);

// تحقق من وجود APP_KEY
$needsKey = false;
if (strpos($envContent, 'APP_KEY=') === false) {
    $needsKey = true;
    echo "APP_KEY not found in .env file\n";
} elseif (preg_match('/APP_KEY=\s*$/m', $envContent)) {
    $needsKey = true;
    echo "APP_KEY is empty in .env file\n";
} elseif (preg_match('/APP_KEY=""\s*$/m', $envContent)) {
    $needsKey = true;
    echo "APP_KEY is empty string in .env file\n";
}

if ($needsKey) {
    echo "Generating new application key...\n";
    
    // إنشاء مفتاح جديد
    $key = 'base64:' . base64_encode(random_bytes(32));
    
    // تحديث ملف .env
    if (strpos($envContent, 'APP_KEY=') === false) {
        // إضافة APP_KEY إذا لم يكن موجود
        $envContent = "APP_KEY=$key\n" . $envContent;
    } else {
        // استبدال APP_KEY الموجود
        $envContent = preg_replace('/APP_KEY=.*$/m', "APP_KEY=$key", $envContent);
    }
    
    // كتابة الملف المحدث
    if (file_put_contents($envFile, $envContent)) {
        echo "Application key generated successfully: $key\n";
    } else {
        echo "Error: Could not write to .env file\n";
        exit(1);
    }
} else {
    echo "APP_KEY already exists in .env file\n";
}

echo "Key generation completed successfully!\n";
exit(0);
?>
