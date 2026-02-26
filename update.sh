#!/bin/bash
# =============================================================================
# SEOmaster Update Script
# Ausführen nach jedem git push: bash update.sh
# =============================================================================

APP_DIR="/var/www/seomaster"
GREEN='\033[0;32m'; BLUE='\033[0;34m'; NC='\033[0m'

echo -e "${BLUE}[UPDATE]${NC} SEOmaster wird aktualisiert..."

cd $APP_DIR

# Wartungsmodus
php artisan down

# Code holen
git pull origin main

# Dependencies
composer install --no-dev --optimize-autoloader --no-interaction -q

# Migrations
php artisan migrate --force

# Cache leeren & neu aufbauen
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart

# Berechtigungen
chown -R www-data:www-data storage bootstrap/cache

# Wartungsmodus beenden
php artisan up

echo -e "${GREEN}[OK]${NC} Update abgeschlossen!"
