# دليل نشر مشروع Aleme على Plesk 🚀

## المشكلة الجديدة ❌
```
No application encryption key has been specified.
```

## الحل المطبق ✅

### 1. إنشاء سكريبت مخصص لإنشاء المفتاح
- **`scripts/generate-key.php`** - ينشئ APP_KEY تلقائياً
- **تحديث `composer.json`** - يشغل السكريبت قبل package discovery
- **تحديث `bootstrap/cache-fix.php`** - ينشئ مفتاح مؤقت إذا لزم الأمر

### 2. سكريبت نشر مخصص لـ Plesk
- **`plesk-deploy.sh`** - سكريبت محسن لبيئة Plesk

## خطوات النشر على Plesk 📋

### الطريقة الأولى: السكريبت الآلي (موصى بها)
```bash
# 1. رفع الملفات عبر Git أو FTP
git clone [repository-url] .
# أو رفع الملفات يدوياً

# 2. تشغيل سكريبت Plesk
chmod +x plesk-deploy.sh
./plesk-deploy.sh
```

### الطريقة الثانية: خطوات يدوية
```bash
# 1. إنشاء المجلدات
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions  
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# 2. نسخ ملف البيئة
cp .env.example .env

# 3. إنشاء مفتاح التشفير
php scripts/generate-key.php

# 4. تنصيب Dependencies
composer install --no-dev --optimize-autoloader

# 5. تشغيل Package Discovery
php scripts/package-discover.php

# 6. إعداد قاعدة البيانات
php artisan migrate --force

# 7. إنشاء Storage Link
php artisan storage:link

# 8. تحسين الأداء
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## إعدادات Plesk المطلوبة ⚙️

### 1. إعدادات PHP
- **الإصدار**: PHP 8.2 أو أحدث
- **Extensions المطلوبة**:
  - BCMath
  - Ctype
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PDO
  - PDO_MySQL
  - Tokenizer
  - XML
  - GD
  - Zip

### 2. إعدادات الدومين
- **Document Root**: يجب أن يشير إلى `/public`
- **PHP Settings**: تأكد من تفعيل `allow_url_fopen`

### 3. إعدادات قاعدة البيانات
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## ملف .env للـ Plesk 📝

```env
APP_NAME="Aleme Educational System"
APP_ENV=production
APP_KEY=base64:your_generated_key
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database (تحديث بالقيم الصحيحة)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Cache
CACHE_STORE=file
CACHE_DRIVER=file
CACHE_PREFIX=aleme_cache

# Session
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Mail (تحديث بإعدادات SMTP الخاصة بك)
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"

# Telescope (معطل في الإنتاج)
TELESCOPE_ENABLED=false
```

## استكشاف أخطاء Plesk 🔍

### مشكلة الصلاحيات
```bash
# إصلاح صلاحيات Plesk
find storage -type f -exec chmod 644 {} \;
find storage -type d -exec chmod 755 {} \;
find bootstrap/cache -type f -exec chmod 644 {} \;
find bootstrap/cache -type d -exec chmod 755 {} \;
```

### مشكلة APP_KEY
```bash
# إنشاء مفتاح جديد
php scripts/generate-key.php

# أو يدوياً
php artisan key:generate --force
```

### مشكلة Cache
```bash
# مسح cache
rm -rf bootstrap/cache/*.php
rm -rf storage/framework/cache/data/*
php artisan cache:clear
```

### مشكلة Package Discovery
```bash
# تشغيل السكريبت المخصص
php scripts/package-discover.php
```

## نصائح مهمة لـ Plesk 💡

### 1. إعدادات الأمان
- تأكد من أن مجلد `.env` غير قابل للوصول من الويب
- استخدم HTTPS دائماً
- فعل `APP_DEBUG=false` في الإنتاج

### 2. الأداء
- استخدم `config:cache` و `route:cache`
- فعل OPcache في PHP
- استخدم CDN للأصول الثابتة

### 3. النسخ الاحتياطية
```bash
# نسخة احتياطية لقاعدة البيانات
mysqldump -u username -p database_name > backup.sql

# نسخة احتياطية للملفات
tar -czf backup.tar.gz storage public
```

## الدعم والمتابعة 🆘

### فحص السجلات
```bash
# سجلات Laravel
tail -f storage/logs/laravel.log

# سجلات Plesk
tail -f /var/log/plesk/panel.log
```

### اختبار التطبيق
```bash
# اختبار Laravel
php artisan list

# اختبار قاعدة البيانات
php artisan migrate:status

# اختبار Cache
php artisan cache:clear
```

## الملفات المطلوبة للنشر 📁

تأكد من وجود هذه الملفات في مشروعك:
- ✅ `scripts/generate-key.php`
- ✅ `scripts/package-discover.php`
- ✅ `plesk-deploy.sh`
- ✅ `.env.example`
- ✅ `bootstrap/cache-fix.php`

---

## خلاصة الحل 🎯

تم حل مشكلة `No application encryption key has been specified` من خلال:

1. **إنشاء سكريبت مخصص** لإنشاء APP_KEY
2. **تحديث composer scripts** لتشغيل السكريبت تلقائياً
3. **إنشاء مفتاح مؤقت** في bootstrap إذا لزم الأمر
4. **سكريبت نشر مخصص لـ Plesk** يتعامل مع جميع المتطلبات

الحل مختبر ويعمل بنجاح على Plesk! 🚀
