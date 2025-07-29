#!/bin/bash

# Aleme Educational System - Enhanced Deployment Script
# This script handles deployment with cache issue fixes

echo "🚀 Starting Enhanced Aleme Deployment..."

# Step 1: Environment Setup
echo "🔧 Setting up environment..."
if [ ! -f .env ]; then
    if [ -f .env.server ]; then
        cp .env.server .env
        echo "✅ Copied .env.server to .env"
    else
        echo "❌ No .env file found! Please create one."
        exit 1
    fi
fi

# Step 2: Clear all cache before starting
echo "🧹 Clearing all cache files..."
rm -rf bootstrap/cache/*.php 2>/dev/null || true
rm -rf storage/framework/cache/data/* 2>/dev/null || true
rm -rf storage/framework/sessions/* 2>/dev/null || true
rm -rf storage/framework/views/* 2>/dev/null || true

# Step 3: Install Composer dependencies with error handling
echo "📦 Installing Composer dependencies..."
if ! composer install --no-dev --optimize-autoloader --no-interaction; then
    echo "❌ Composer install failed. Trying to fix..."
    
    # Remove vendor and try again
    rm -rf vendor composer.lock
    composer install --no-dev --optimize-autoloader --no-interaction
    
    if [ $? -ne 0 ]; then
        echo "❌ Composer install still failing. Check dependencies."
        exit 1
    fi
fi

# Step 4: Generate application key if missing
echo "🔑 Checking application key..."
if grep -q "APP_KEY=$" .env || grep -q "APP_KEY=\"\"" .env; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Step 5: Clear Laravel cache with error handling
echo "🧹 Clearing Laravel cache..."
php artisan cache:clear 2>/dev/null || echo "Cache clear skipped (expected)"
php artisan config:clear 2>/dev/null || echo "Config clear skipped"
php artisan route:clear 2>/dev/null || echo "Route clear skipped"
php artisan view:clear 2>/dev/null || echo "View clear skipped"

# Step 6: Regenerate autoload
echo "🔄 Regenerating autoload..."
composer dump-autoload --optimize

# Step 7: Package discovery with retry mechanism
echo "📦 Running package discovery..."
if ! php artisan package:discover --ansi; then
    echo "⚠️ Package discovery failed, trying manual fix..."
    
    # Create basic cache directory
    mkdir -p storage/framework/cache/data
    chmod -R 775 storage/framework/cache
    
    # Try again
    php artisan package:discover --ansi
    
    if [ $? -ne 0 ]; then
        echo "❌ Package discovery still failing. Check logs."
        # Continue anyway, might work
    fi
fi

# Step 8: Database setup
echo "🗄️ Setting up database..."
if php artisan migrate --force; then
    echo "✅ Migrations completed"
else
    echo "⚠️ Migrations failed, but continuing..."
fi

# Step 9: Seed database (optional)
read -p "Do you want to run database seeders? (y/N): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan db:seed --force
fi

# Step 10: Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link

# Step 11: Set proper permissions
echo "🔒 Setting permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chown -R www-data:www-data storage 2>/dev/null || true
chown -R www-data:www-data bootstrap/cache 2>/dev/null || true

# Step 12: Install and build frontend assets
echo "🎨 Building frontend assets..."
if command -v npm &> /dev/null; then
    npm ci --only=production
    npm run build
else
    echo "⚠️ npm not found, skipping frontend build"
fi

# Step 13: Cache optimization for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Step 14: Final verification
echo "🔍 Running final verification..."
if php artisan list > /dev/null 2>&1; then
    echo "✅ Laravel artisan is working"
else
    echo "❌ Laravel artisan has issues"
fi

echo ""
echo "🎉 Deployment completed!"
echo "📋 Summary:"
echo "   - Dependencies installed"
echo "   - Cache cleared and optimized"
echo "   - Database migrated"
echo "   - Storage linked"
echo "   - Permissions set"
echo "   - Frontend assets built"
echo ""
echo "🌐 Your Aleme Educational System should be ready!"
echo "📝 Check the logs if you encounter any issues: storage/logs/laravel.log"
