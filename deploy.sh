#!/bin/bash

# Aleme Educational System Deployment Script
# This script handles the deployment process for the Laravel application

echo "🚀 Starting Aleme Educational System Deployment..."

# Step 1: Clear any existing cache
echo "📦 Clearing application cache..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Step 2: Install/Update Composer dependencies (production only)
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Step 3: Generate application key if not exists
echo "🔑 Checking application key..."
if grep -q "APP_KEY=$" .env; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Step 4: Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Step 5: Seed database if needed (uncomment if required)
# echo "🌱 Seeding database..."
# php artisan db:seed --force

# Step 6: Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link

# Step 7: Cache configuration for production
echo "⚡ Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Step 8: Install and build frontend assets
echo "🎨 Building frontend assets..."
npm ci --only=production
npm run build

# Step 9: Set proper permissions
echo "🔒 Setting permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

echo "✅ Deployment completed successfully!"
echo "🌐 Your Aleme Educational System is ready!"
