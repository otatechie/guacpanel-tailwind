#!/bin/bash

# Pull latest changes
git pull

# Clear Laravel caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Clear compiled views
php artisan view:cache

# Install/update dependencies if needed
composer install --no-interaction --prefer-dist --optimize-autoloader
npm install

# Rebuild assets
npm run build

# Optimize Laravel
php artisan optimize

echo "Refresh complete! Don't forget to clear your browser cache." 