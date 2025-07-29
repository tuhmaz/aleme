#!/bin/bash

# Plesk Deployment Script for Aleme Educational System
# This script is specifically designed for Plesk hosting environments

echo "🚀 Starting Plesk Deployment for Aleme Educational System..."

# Step 1: Create necessary directories first
echo "📁 Creating required directories..."
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# Step 2: Set permissions for Plesk
echo "🔒 Setting Plesk-compatible permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Step 3: Create .env file if it doesn't exist
echo "📋 Setting up environment file..."
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        cp .env.example .env
        echo "✅ Created .env from .env.example"
    else
        echo "❌ No .env.example found!"
        exit 1
    fi
fi

# Step 4: Generate application key using our custom script
echo "🔑 Generating application key..."
php scripts/generate-key.php

# Step 5: Install Composer dependencies
echo "📦 Installing Composer dependencies..."
if command -v composer &> /dev/null; then
    composer install --no-dev --optimize-autoloader --no-interaction
else
    echo "❌ Composer not found! Please install Composer first."
    exit 1
fi

# Step 6: Clear any existing cache
echo "🧹 Clearing cache..."
rm -rf bootstrap/cache/*.php 2>/dev/null || true
rm -rf storage/framework/cache/data/* 2>/dev/null || true

# Step 7: Run our custom package discovery
echo "📦 Running package discovery..."
php scripts/package-discover.php

# Step 8: Database setup
echo "🗄️ Setting up database..."
if php artisan migrate --force; then
    echo "✅ Database migrations completed"
else
    echo "⚠️ Database migrations failed, check your DB settings in .env"
fi

# Step 9: Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link

# Step 10: Install Node.js dependencies (if available)
echo "🎨 Building frontend assets..."
if command -v npm &> /dev/null; then
    npm ci --only=production
    npm run build
else
    echo "⚠️ npm not found, skipping frontend build"
fi

# Step 11: Cache optimization for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Step 12: Final permissions for Plesk
echo "🔒 Setting final permissions..."
find storage -type f -exec chmod 644 {} \;
find storage -type d -exec chmod 755 {} \;
find bootstrap/cache -type f -exec chmod 644 {} \;
find bootstrap/cache -type d -exec chmod 755 {} \;

# Step 13: Test the deployment
echo "🔍 Testing deployment..."
if php artisan list > /dev/null 2>&1; then
    echo "✅ Laravel is working correctly!"
else
    echo "❌ Laravel has issues, check the logs"
fi

echo ""
echo "🎉 Plesk deployment completed!"
echo "📝 Important notes for Plesk:"
echo "   - Make sure your document root points to /public"
echo "   - Ensure PHP version is 8.2 or higher"
echo "   - Check that all required PHP extensions are enabled"
echo "   - Update your database settings in .env file"
echo ""
echo "🌐 Your Aleme Educational System should now be ready!"
echo "📋 If you encounter issues, check storage/logs/laravel.log"
