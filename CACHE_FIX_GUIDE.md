# دليل إصلاح مشكلة Cache في مشروع Aleme

## المشكلة
```
Target class [cache] does not exist.
Class "cache" does not exist
```

## الأسباب المحتملة
1. ملفات Cache تالفة أو مفقودة
2. مشكلة في autoload
3. إعدادات Cache غير صحيحة
4. مشكلة في Service Provider

## الحلول

### الحل الأول: تنظيف Cache يدوياً

#### على Windows:
```cmd
# تشغيل ملف الإصلاح
fix-cache-issue.bat
```

#### على Linux/Mac:
```bash
# تشغيل ملف الإصلاح
chmod +x fix-cache-issue.sh
./fix-cache-issue.sh
```

### الحل الثاني: خطوات يدوية مفصلة

#### 1. مسح ملفات Cache
```bash
# مسح ملفات bootstrap cache
rm -rf bootstrap/cache/*.php

# مسح ملفات storage cache
rm -rf storage/framework/cache/data/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*
```

#### 2. مسح Laravel Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

#### 3. إعادة بناء Autoload
```bash
composer dump-autoload --optimize
```

#### 4. إعادة اكتشاف Packages
```bash
php artisan package:discover --ansi
```

### الحل الثالث: إصلاح إعدادات Cache

#### تحديث ملف .env
تأكد من وجود هذه الإعدادات في `.env`:
```env
CACHE_DRIVER=file
CACHE_PREFIX=laravel_cache

# أو استخدام database
CACHE_DRIVER=database
```

#### إنشاء جدول Cache (إذا كنت تستخدم database cache)
```bash
php artisan cache:table
php artisan migrate
```

### الحل الرابع: إعادة تنصيب Dependencies

```bash
# حذف vendor و composer.lock
rm -rf vendor
rm composer.lock

# إعادة التنصيب
composer install --no-dev --optimize-autoloader
```

### الحل الخامس: إصلاح Service Providers

تحقق من ملف `config/app.php` وتأكد من وجود:
```php
'providers' => [
    // ...
    Illuminate\Cache\CacheServiceProvider::class,
    // ...
],
```

### الحل السادس: إنشاء Cache Config يدوياً

إذا فشلت كل الحلول، قم بإنشاء ملف cache config يدوياً:

```php
// config/cache.php
<?php
return [
    'default' => env('CACHE_DRIVER', 'file'),
    'stores' => [
        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
        ],
        'database' => [
            'driver' => 'database',
            'table' => 'cache',
            'connection' => null,
        ],
    ],
    'prefix' => env('CACHE_PREFIX', 'laravel_cache'),
];
```

## خطوات التحقق

بعد تطبيق الحلول، تحقق من:

1. **تشغيل artisan**:
```bash
php artisan list
```

2. **تشغيل package discovery**:
```bash
php artisan package:discover --ansi
```

3. **تشغيل cache**:
```bash
php artisan cache:clear
```

## نصائح الوقاية

1. **تأكد من الصلاحيات**:
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

2. **استخدم .env صحيح**:
```env
CACHE_DRIVER=file
SESSION_DRIVER=file
```

3. **تجنب تشغيل artisan بصلاحيات مختلفة**

## إذا استمرت المشكلة

1. تحقق من ملفات logs: `storage/logs/laravel.log`
2. تأكد من إصدار PHP (يجب أن يكون 8.2+)
3. تحقق من extensions المطلوبة
4. جرب إعادة تنصيب Laravel من الصفر

## أوامر الطوارئ

```bash
# إعادة تعيين كامل
rm -rf vendor composer.lock bootstrap/cache/*.php
rm -rf storage/framework/cache/data/*
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan package:discover --ansi
```
