# دليل نشر مشروع Aleme التعليمي

## المشاكل الشائعة وحلولها

### 1. مشكلة PSR-4 Autoloading
**الخطأ**: `Class App\Http\Controllers\Controller located in ./app/app/Http/Controllers/Controller.php does not comply with psr-4 autoloading standard`

**السبب**: وجود مجلد `app` مكرر في المسار

**الحل**:
```bash
# تأكد من أن بنية المجلدات صحيحة
ls -la app/
# يجب أن تكون: app/Http/Controllers/Controller.php
# وليس: app/app/Http/Controllers/Controller.php
```

### 2. مشكلة Laravel Fortify
**الخطأ**: `Class "Laravel\Fortify\Features" not found`

**الحل**:
```bash
# إعادة تنصيب Laravel Fortify
composer require laravel/fortify
php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"
```

### 3. خطوات النشر الصحيحة

#### أ. التحضير للنشر
```bash
# 1. نسخ المشروع للسيرفر
git clone [repository-url] aleme
cd aleme

# 2. نسخ ملف البيئة
cp .env.production .env

# 3. تحديث إعدادات قاعدة البيانات في .env
nano .env
```

#### ب. تنصيب التبعيات
```bash
# 1. تنصيب Composer dependencies (إنتاج فقط)
composer install --no-dev --optimize-autoloader

# 2. إنشاء مفتاح التطبيق
php artisan key:generate

# 3. تنصيب Node.js dependencies
npm ci --only=production
```

#### ج. إعداد قاعدة البيانات
```bash
# 1. إنشاء قاعدة البيانات
mysql -u root -p -e "CREATE DATABASE JO_data CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 2. تشغيل Migrations
php artisan migrate --force

# 3. تشغيل Seeders (اختياري)
php artisan db:seed --force
```

#### د. إعداد الملفات والصلاحيات
```bash
# 1. إنشاء رابط التخزين
php artisan storage:link

# 2. تعيين الصلاحيات
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache

# 3. بناء الأصول
npm run build
```

#### هـ. تحسين الأداء
```bash
# 1. تخزين التكوين مؤقتاً
php artisan config:cache

# 2. تخزين المسارات مؤقتاً
php artisan route:cache

# 3. تخزين العروض مؤقتاً
php artisan view:cache

# 4. تحسين Autoloader
composer dump-autoload --optimize
```

### 4. إعداد خادم الويب

#### Apache (.htaccess)
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

#### Nginx
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/aleme/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 5. متطلبات السيرفر

- **PHP**: 8.2 أو أحدث
- **Extensions**: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, GD, Zip
- **MySQL**: 5.7 أو أحدث / MariaDB 10.3 أو أحدث
- **Node.js**: 18 أو أحدث
- **Composer**: 2.0 أو أحدث

### 6. استكشاف الأخطاء

#### مشكلة الصلاحيات
```bash
# إصلاح صلاحيات Laravel
sudo chown -R www-data:www-data /path/to/aleme
sudo chmod -R 755 /path/to/aleme
sudo chmod -R 775 /path/to/aleme/storage
sudo chmod -R 775 /path/to/aleme/bootstrap/cache
```

#### مشكلة Autoload
```bash
# إعادة بناء Autoload
composer dump-autoload
php artisan clear-compiled
php artisan optimize
```

#### مشكلة Cache
```bash
# مسح جميع أنواع Cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 7. الأمان

- تأكد من تعيين `APP_DEBUG=false` في الإنتاج
- استخدم HTTPS
- قم بتحديث جميع كلمات المرور الافتراضية
- فعل Firewall
- قم بعمل نسخ احتياطية منتظمة

### 8. المراقبة

- راقب ملفات السجلات: `storage/logs/laravel.log`
- استخدم Laravel Telescope للتطوير فقط
- راقب استخدام قاعدة البيانات والذاكرة

---

## الدعم

إذا واجهت أي مشاكل، تأكد من:
1. مراجعة ملفات السجلات
2. التحقق من إعدادات قاعدة البيانات
3. التأكد من صحة صلاحيات الملفات
4. مراجعة إعدادات خادم الويب
