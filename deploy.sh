#!/bin/bash
# =============================================================================
# SEOmaster Deploy Script - Ubuntu 22.04
# shopware-seomaster.com
# =============================================================================

set -e  # Exit on error

# ── KONFIGURATION ─────────────────────────────────────────────────────────────
DOMAIN="shopware-seomaster.com"
APP_DIR="/var/www/seomaster"
DB_NAME="seomaster"
DB_USER="seomaster"
GITHUB_REPO="https://github.com/IHR-USERNAME/seomaster.git"  # ← ANPASSEN!

# Farben
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; BLUE='\033[0;34m'; NC='\033[0m'
info()    { echo -e "${BLUE}[INFO]${NC} $1"; }
success() { echo -e "${GREEN}[OK]${NC} $1"; }
warn()    { echo -e "${YELLOW}[WARN]${NC} $1"; }
error()   { echo -e "${RED}[ERROR]${NC} $1"; exit 1; }

echo -e "${BLUE}"
echo "╔══════════════════════════════════════════════════╗"
echo "║          SEOmaster Deploy Script                 ║"
echo "║          Ubuntu 22.04 + nginx + PHP 8.3          ║"
echo "╚══════════════════════════════════════════════════╝"
echo -e "${NC}"

# ── ROOT CHECK ────────────────────────────────────────────────────────────────
if [ "$EUID" -ne 0 ]; then
  error "Bitte als root ausführen: sudo bash deploy.sh"
fi

# ── DATENBANK PASSWORT GENERIEREN ─────────────────────────────────────────────
DB_PASS=$(openssl rand -base64 24 | tr -d "=+/" | head -c 20)
APP_KEY=""

# ── STEP 1: SYSTEM UPDATE ─────────────────────────────────────────────────────
info "System wird aktualisiert..."
apt update -qq && apt upgrade -y -qq
success "System aktualisiert"

# ── STEP 2: PHP 8.3 ───────────────────────────────────────────────────────────
info "PHP 8.3 wird installiert..."
apt install -y -qq software-properties-common
add-apt-repository ppa:ondrej/php -y -qq
apt update -qq
apt install -y -qq \
    php8.3-fpm php8.3-cli php8.3-mysql php8.3-curl \
    php8.3-xml php8.3-zip php8.3-mbstring php8.3-bcmath \
    php8.3-intl php8.3-redis php8.3-gd
success "PHP 8.3 installiert: $(php -v | head -1)"

# ── STEP 3: NGINX ─────────────────────────────────────────────────────────────
info "nginx wird installiert..."
apt install -y -qq nginx
systemctl enable nginx
success "nginx installiert"

# ── STEP 4: MARIADB ───────────────────────────────────────────────────────────
info "MariaDB wird installiert..."
apt install -y -qq mariadb-server
systemctl enable mariadb

# Datenbank + User anlegen
mysql -u root <<MYSQL
CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
MYSQL
success "MariaDB: Datenbank '${DB_NAME}' angelegt"

# ── STEP 5: COMPOSER ──────────────────────────────────────────────────────────
info "Composer wird installiert..."
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer -q
success "Composer: $(composer --version 2>/dev/null | head -1)"

# ── STEP 6: GIT + CODE ────────────────────────────────────────────────────────
info "Code von GitHub wird geklont..."
apt install -y -qq git
mkdir -p $APP_DIR
git clone $GITHUB_REPO $APP_DIR
cd $APP_DIR
success "Code geklont nach $APP_DIR"

# ── STEP 7: COMPOSER INSTALL ──────────────────────────────────────────────────
info "PHP Dependencies werden installiert..."
cd $APP_DIR
composer install --no-dev --optimize-autoloader --no-interaction -q
success "Dependencies installiert"

# ── STEP 8: .ENV ERSTELLEN ────────────────────────────────────────────────────
info ".env wird konfiguriert..."
cp .env.example .env

# App Key generieren
APP_KEY=$(php artisan key:generate --show)

# .env Werte setzen
sed -i "s|APP_NAME=.*|APP_NAME=SEOmaster|" .env
sed -i "s|APP_ENV=.*|APP_ENV=production|" .env
sed -i "s|APP_DEBUG=.*|APP_DEBUG=false|" .env
sed -i "s|APP_URL=.*|APP_URL=https://${DOMAIN}|" .env
sed -i "s|DB_DATABASE=.*|DB_DATABASE=${DB_NAME}|" .env
sed -i "s|DB_USERNAME=.*|DB_USERNAME=${DB_USER}|" .env
sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=${DB_PASS}|" .env

success ".env konfiguriert"

# ── STEP 9: LARAVEL SETUP ─────────────────────────────────────────────────────
info "Laravel wird eingerichtet..."
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
success "Laravel eingerichtet + Migrations gelaufen"

# ── STEP 10: PERMISSIONS ──────────────────────────────────────────────────────
info "Berechtigungen werden gesetzt..."
chown -R www-data:www-data $APP_DIR
chmod -R 755 $APP_DIR
chmod -R 775 $APP_DIR/storage $APP_DIR/bootstrap/cache
success "Berechtigungen gesetzt"

# ── STEP 11: NGINX CONFIG ─────────────────────────────────────────────────────
info "nginx wird konfiguriert..."
cat > /etc/nginx/sites-available/seomaster <<NGINX
server {
    listen 80;
    server_name ${DOMAIN} www.${DOMAIN};
    root ${APP_DIR}/public;
    index index.php;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    # Gzip
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Static files caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    client_max_body_size 20M;
}
NGINX

ln -sf /etc/nginx/sites-available/seomaster /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx
success "nginx konfiguriert"

# ── STEP 12: CERTBOT / SSL ────────────────────────────────────────────────────
info "SSL-Zertifikat wird eingerichtet..."
apt install -y -qq certbot python3-certbot-nginx
certbot --nginx -d $DOMAIN -d www.$DOMAIN \
    --non-interactive --agree-tos \
    --email admin@${DOMAIN} \
    --redirect
success "SSL-Zertifikat installiert (auto-renewal aktiv)"

# ── STEP 13: CRON (Laravel Scheduler) ────────────────────────────────────────
info "Laravel Scheduler wird eingerichtet..."
(crontab -l 2>/dev/null; echo "* * * * * cd ${APP_DIR} && php artisan schedule:run >> /dev/null 2>&1") | crontab -
success "Cron eingerichtet"

# ── FERTIG ────────────────────────────────────────────────────────────────────
echo ""
echo -e "${GREEN}"
echo "╔══════════════════════════════════════════════════╗"
echo "║           ✅ DEPLOY ERFOLGREICH!                 ║"
echo "╚══════════════════════════════════════════════════╝"
echo -e "${NC}"
echo ""
echo -e "${YELLOW}WICHTIGE INFORMATIONEN - BITTE SPEICHERN:${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo -e "🌐 URL:          ${GREEN}https://${DOMAIN}${NC}"
echo -e "📁 App-Pfad:     ${GREEN}${APP_DIR}${NC}"
echo -e "🗄  DB Name:      ${GREEN}${DB_NAME}${NC}"
echo -e "🗄  DB User:      ${GREEN}${DB_USER}${NC}"
echo -e "🗄  DB Passwort:  ${GREEN}${DB_PASS}${NC}"
echo ""
echo -e "${YELLOW}NÄCHSTE SCHRITTE:${NC}"
echo "1. .env anpassen: nano ${APP_DIR}/.env"
echo "   → OPENAI_API_KEY, PAYPAL_CLIENT_ID, etc. eintragen"
echo "2. php artisan db:seed (falls Seed-Daten nötig)"
echo "3. Website testen: https://${DOMAIN}"
echo ""
echo -e "${BLUE}UPDATE-BEFEHL für später:${NC}"
echo "cd ${APP_DIR} && git pull && composer install --no-dev && php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache"
echo ""
