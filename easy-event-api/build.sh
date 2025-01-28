#!/usr/bin/env bash
# Exit on error
set -o errexit

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Storage link
php artisan storage:link

# Database migrations
php artisan migrate --force

# Install npm dependencies
npm install

# Build assets
npm run build

# Optimize
php artisan optimize