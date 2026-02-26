# ⚡ Lavarell — Installations-Anleitung

## Schnellstart (5 Minuten)

```bash
# 1. Composer-Dependencies installieren
composer install --no-dev --optimize-autoloader

# 2. Node-Dependencies + Assets bauen
npm install && npm run build

# 3. Setup-Wizard starten
php artisan lavarell:install
```

Der interaktive Wizard führt durch alle Schritte.

---

## Schritt-für-Schritt (manuell)

### 1. Voraussetzungen

| Anforderung | Version |
|---|---|
| PHP | ≥ 8.2 |
| MySQL / MariaDB | ≥ 8.0 / ≥ 10.4 |
| Redis | ≥ 6.0 (empfohlen) |
| Composer | ≥ 2.0 |
| Node.js | ≥ 18.0 |

**Benötigte PHP-Extensions:**
`pdo`, `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `curl`, `bcmath`

---

### 2. .env konfigurieren

```bash
cp .env.example .env
```

Mindestpflichtfelder:

```env
APP_URL=https://deine-domain.de
APP_KEY=          # wird von artisan key:generate gesetzt

DB_HOST=127.0.0.1
DB_DATABASE=lavarell
DB_USERNAME=lavarell_user
DB_PASSWORD=sicheres_passwort

MAIL_FROM_ADDRESS=noreply@deine-domain.de
MAIL_FROM_NAME=Lavarell

QUEUE_CONNECTION=redis   # oder: database / sync (nur lokal)
CACHE_STORE=redis
SESSION_DRIVER=redis
```

---

### 3. Datenbank + Seeders

```bash
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=RolesAndPermissionsSeeder
php artisan db:seed --class=SubscriptionPlanSeeder
php artisan storage:link
```

---

### 4. PayPal einrichten

1. **App anlegen:** [developer.paypal.com](https://developer.paypal.com) → Apps & Credentials → App erstellen  
   → Client ID + Secret in `.env` eintragen

2. **Webhook erstellen:**  
   → Webhooks → Add Webhook  
   → URL: `https://deine-domain.de/webhooks/paypal`  
   → Events aktivieren:
   ```
   BILLING.SUBSCRIPTION.ACTIVATED
   BILLING.SUBSCRIPTION.CANCELLED
   BILLING.SUBSCRIPTION.EXPIRED
   BILLING.SUBSCRIPTION.SUSPENDED
   BILLING.SUBSCRIPTION.UPDATED
   BILLING.SUBSCRIPTION.PAYMENT.FAILED
   PAYMENT.SALE.COMPLETED
   PAYMENT.SALE.REFUNDED
   ```
   → Webhook ID → `PAYPAL_WEBHOOK_ID` in `.env`

3. **Billing Plans anlegen:**  
   → Subscriptions → Plans → Create Plan (für jeden Lavarell-Plan)  
   → Plan-IDs in `.env`:
   ```env
   PAYPAL_PLAN_STARTER_MONTHLY=P-XXXXX
   PAYPAL_PLAN_STARTER_YEARLY=P-XXXXX
   PAYPAL_PLAN_PRO_MONTHLY=P-XXXXX
   PAYPAL_PLAN_PRO_YEARLY=P-XXXXX
   PAYPAL_PLAN_AGENCY_MONTHLY=P-XXXXX
   PAYPAL_PLAN_AGENCY_YEARLY=P-XXXXX
   ```
   → Seeder erneut ausführen: `php artisan db:seed --class=SubscriptionPlanSeeder`

---

### 5. Crontab (Server)

```bash
crontab -e
```

Folgende Zeile hinzufügen:

```
* * * * * cd /var/www/lavarell && php artisan schedule:run >> /dev/null 2>&1
```

**Was der Scheduler ausführt:**

| Zeit | Job | Beschreibung |
|---|---|---|
| tägl. 00:05 | `ExpireTrialsJob` | Abgelaufene Trials → cancelled |
| tägl. 09:00 | `SendTrialWarningJob(3)` | Mail 3 Tage vor Trial-Ende |
| tägl. 09:00 | `SendTrialWarningJob(1)` | Mail 1 Tag vor Trial-Ende |
| tägl. 10:00 | `SendRenewalReminderJob(14/3)` | Verlängerungs-Erinnerung |
| tägl. 11:00 | `SendPaymentFailedReminderJob` | Zahlungs-Mahnungen + Suspend |
| So. 03:00 | `CleanupOldDataJob` | Logs, Pending-Subs, GDPR-Purge |

---

### 6. Queue Worker (Supervisor empfohlen)

```bash
# Einmalig testen:
php artisan queue:work --queue=default,mail --tries=3 --sleep=3

# Mit Supervisor (Produktions-Setup):
# /etc/supervisor/conf.d/lavarell-worker.conf
```

```ini
[program:lavarell-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/lavarell/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/lavarell/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
supervisorctl reread && supervisorctl update && supervisorctl start lavarell-worker:*
```

---

### 7. Nginx-Config (Beispiel)

```nginx
server {
    listen 443 ssl http2;
    server_name lavarell.de www.lavarell.de;
    root /var/www/lavarell/public;
    index index.php;

    ssl_certificate     /etc/letsencrypt/live/lavarell.de/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/lavarell.de/privkey.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## Nützliche Artisan-Befehle

```bash
# Setup
php artisan lavarell:install          # Setup-Wizard
php artisan lavarell:status           # Abo-Übersicht

# Jobs manuell auslösen
php artisan lavarell:expire-trials
php artisan lavarell:trial-warnings --days=3
php artisan lavarell:renewal-reminders --days=14
php artisan lavarell:payment-reminders
php artisan lavarell:cleanup

# Maintenance
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart              # Nach Deployments!

# Schedule debuggen
php artisan schedule:list
php artisan schedule:work              # Lokal
```

---

## Admin-Panel

**URL:** `https://deine-domain.de/admin`

Standard-Zugangsdaten nach Seeder:
```
E-Mail:    admin@lavarell.com
Passwort:  changeme123!
```

⚠️ **Sofort ändern!**

---

## Support

- GitHub Issues: [github.com/lavarell/lavarell](https://github.com/lavarell/lavarell)
- E-Mail: support@lavarell.com
