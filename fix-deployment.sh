#!/bin/bash

# Fix Deployment Script for Aleme Educational System
# This script fixes the cache issue permanently

echo "🔧 Fixing Aleme Deployment Issues..."

# Step 1: Create necessary directories
echo "📁 Creating cache directories..."
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# Step 2: Set proper permissions
echo "🔒 Setting permissions..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Step 3: Clear all cache
echo "🧹 Clearing cache..."
rm -rf bootstrap/cache/*.php 2>/dev/null || true
rm -rf storage/framework/cache/data/* 2>/dev/null || true
rm -rf storage/framework/sessions/* 2>/dev/null || true
rm -rf storage/framework/views/* 2>/dev/null || true

# Step 4: Copy .env.example if .env doesn't exist
if [ ! -f .env ]; then
    echo "📋 Creating .env file..."
    cp .env.example .env
fi

# Step 5: Install composer dependencies
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Step 6: Generate app key if needed
echo "🔑 Generating application key..."
if grep -q "APP_KEY=$" .env || grep -q "APP_KEY=\"\"" .env; then
    php artisan key:generate --force
fi

# Step 7: Clear Laravel cache (with error handling)
echo "🧹 Clearing Laravel cache..."
php artisan cache:clear 2>/dev/null || echo "Cache clear skipped"
php artisan config:clear 2>/dev/null || echo "Config clear skipped"
php artisan route:clear 2>/dev/null || echo "Route clear skipped"
php artisan view:clear 2>/dev/null || echo "View clear skipped"

# Step 8: Regenerate autoload
echo "🔄 Regenerating autoload..."
composer dump-autoload --optimize

# Step 9: Package discovery
echo "📦 Running package discovery..."
php artisan package:discover --ansi

# Step 10: Run migrations
echo "🗄️ Running migrations..."
php artisan migrate --force

# Step 11: Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link

# Step 12: Cache for production
echo "⚡ Caching for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Deployment fix completed!"
echo "🌐 Your application should now work correctly!"
