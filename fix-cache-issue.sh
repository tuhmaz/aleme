#!/bin/bash

# Fix Cache Issue Script for Aleme Educational System
echo "🔧 Fixing Cache Issue..."

# Step 1: Clear all cache files
echo "🧹 Clearing all cache..."
rm -rf bootstrap/cache/*.php
rm -rf storage/framework/cache/data/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*

# Step 2: Clear Laravel cache
echo "🧹 Clearing Laravel cache..."
php artisan cache:clear 2>/dev/null || echo "Cache clear skipped (expected if cache not working)"
php artisan config:clear 2>/dev/null || echo "Config clear skipped"
php artisan route:clear 2>/dev/null || echo "Route clear skipped"
php artisan view:clear 2>/dev/null || echo "View clear skipped"

# Step 3: Regenerate autoload
echo "🔄 Regenerating autoload..."
composer dump-autoload --optimize

# Step 4: Try package discovery without cache
echo "📦 Running package discovery..."
php artisan package:discover --ansi

# Step 5: If still failing, try manual config cache
if [ $? -ne 0 ]; then
    echo "⚠️ Package discovery failed, trying manual fix..."
    
    # Create basic cache config manually
    php -r "
    \$config = [
        'default' => 'file',
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
    ];
    file_put_contents('bootstrap/cache/config.php', '<?php return ' . var_export(['cache' => \$config], true) . ';');
    "
    
    # Try again
    php artisan package:discover --ansi
fi

echo "✅ Cache fix completed!"
