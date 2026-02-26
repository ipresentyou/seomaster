# ⚡ Lavarell — SEO Automation für Shopware

> KI-gestützte SEO-Plattform als Laravel 11 SaaS. Optimiert Produkte, Kategorien und Bild-Alt-Texte in Shopware-Shops automatisch per GPT — mit PayPal-Billing, Admin-Panel und Onboarding-Wizard.

---

## 🗂 Projektstruktur

```
lavarell/
├── app/
│   ├── Console/Commands/     InstallCommand (php artisan lavarell:install)
│   ├── Filament/             Admin-Panel (5 Resources, 4 Widgets, Dashboard)
│   ├── Http/
│   │   ├── Controllers/      12 Controllers (Auth, SEO, Onboarding, ...)
│   │   └── Middleware/       3 Middlewares
│   ├── Jobs/                 5 Scheduled Jobs (Trial, Billing, Cleanup)
│   ├── Mail/                 6 Transaktions-Mails
│   ├── Models/               6 Eloquent Models
│   ├── Providers/            AdminPanelProvider (Filament)
│   └── Services/             AiService, PayPalService, ShopwareApiService
├── database/
│   ├── migrations/           5 Migrations
│   └── seeders/              RolesAndPermissionsSeeder, SubscriptionPlanSeeder
├── resources/views/
│   ├── auth/                 6 Auth-Views (Login, Register, ...)
│   ├── emails/               6 E-Mail-Templates
│   ├── layouts/              3 Layouts (app, guest, onboarding)
│   ├── onboarding/           4 Wizard-Steps
│   └── seo/ + dashboard/     SEO-Tool-Views
└── routes/
    ├── auth.php              Auth-Routen
    ├── console.php           Schedule + Artisan-Kommandos
    └── web.php               Alle Web-Routen
```

---

## 🚀 Schnellstart

```bash
# 1. Abhängigkeiten
composer install --no-dev --optimize-autoloader
npm install && npm run build

# 2. Interaktiver Setup-Wizard
php artisan lavarell:install
```

Der Wizard führt interaktiv durch: .env konfigurieren, DB testen, Migrieren, Seeden, Admin-User anlegen, Crontab-Anweisung ausgeben.

---

## 🧱 Tech-Stack

| Layer | Technologie |
|---|---|
| Framework | Laravel 11, PHP 8.2+ |
| Admin-Panel | Filament 3 (Dark Violet Theme) |
| Auth | Laravel Breeze + Spatie Roles/Permissions |
| Billing | PayPal Subscriptions (srmklive/paypal 3.x) |
| KI / SEO | OpenAI GPT-4o (Vision) + GPT-3.5-turbo |
| Queue/Cache | Redis (empfohlen) |
| Datenbank | MySQL 8+ / MariaDB 10.4+ |

---

## 📦 Module

| # | Modul | Dateien | Kerninhalt |
|---|---|---|---|
| 1 | Foundation | 15 | 5 Migrations, 6 Models, 3 Middleware, Seeders, Routes |
| 2 | Filament Admin | 12 | Panel, 4 Widgets, Dark-Theme (200+ Zeilen CSS) |
| 3 | Blade Views | 8 | App-Layout, Dashboard, Pricing, Credentials-Views |
| 4 | SEO Controllers | 14 | Product/Category/AltText-SEO, 3 Services |
| 5 | PayPal Billing | 6 | PayPalService, Webhooks (8 Events), 2 Mails |
| 6 | Filament Resources | 8 | UserResource, SubscriptionResource, ActivityLogResource |
| 7 | Auth System | 16 | 6 Auth-Views, 4 Controllers, LoginRequest, Gast-Layout |
| 8 | Scheduled Jobs | 15 | 5 Jobs, 4 Mails + Views (Trial, Mahnung, Cleanup) |
| 9 | Install-Command | 3 | `lavarell:install`, 6 Artisan-Kommandos, INSTALL.md |
| 10 | Onboarding | 10 | 4-Schritt-Wizard, Middleware, Layout, Migration |
| **∑** | **Gesamt** | **~110** | |

---

## 🔐 Nutzer-Flow

```
Register → E-Mail-Verifizierung → Login
                                     │
                         Middleware: onboarding_completed_at?
                                     │
                    ┌────────────────┴────────────────┐
                    │  /onboarding/1  Name + Zeitzone   │
                    │  /onboarding/2  Shop verbinden    │
                    │  /onboarding/3  Erstes Projekt    │
                    │  /onboarding/4  Fertig ✓          │
                    └──────────────────────────────────┘
                                     │
                              /dashboard
```

---

## 💳 Subscription-Pläne

| Plan | Monatlich | Jährlich | Shops | API-Calls/Tag |
|---|---|---|---|---|
| Starter | €19 | €190 | 1 | 50 |
| Pro | €49 | €490 | 3 | 300 |
| Agency | €149 | €1.490 | 20 | 2.000 |

→ 14-Tage-Trial auf Starter nach Registrierung (keine Kreditkarte nötig).

---

## 🤖 Scheduled Jobs

| Zeit | Job | Aktion |
|---|---|---|
| tägl. 00:05 | `ExpireTrialsJob` | Abgelaufene Trials → `cancelled` + Expired-Mail |
| tägl. 09:00 | `SendTrialWarningJob(3)` | Warn-Mail 3 Tage vor Trial-Ende |
| tägl. 09:00 | `SendTrialWarningJob(1)` | Warn-Mail 1 Tag vor Trial-Ende |
| tägl. 10:00 | `SendRenewalReminderJob(14/3)` | Verlängerungs-Erinnerung |
| tägl. 11:00 | `SendPaymentFailedReminderJob` | Mahnung Tag 3+7, Suspend nach 7d |
| So. 03:00 | `CleanupOldDataJob` | Logs 90d, Pending-Subs, GDPR-Purge 60d |

```bash
# Einmaliger Crontab-Eintrag auf dem Server:
* * * * * cd /var/www/lavarell && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🛠 Artisan-Kommandos

```bash
# Setup & Status
php artisan lavarell:install
php artisan lavarell:status

# Jobs manuell auslösen
php artisan lavarell:expire-trials
php artisan lavarell:trial-warnings --days=3
php artisan lavarell:renewal-reminders --days=14
php artisan lavarell:payment-reminders
php artisan lavarell:cleanup

# Laravel Standard
php artisan migrate
php artisan db:seed --class=RolesAndPermissionsSeeder
php artisan db:seed --class=SubscriptionPlanSeeder
php artisan config:cache && php artisan route:cache
php artisan queue:work --queue=default,mail --tries=3
php artisan schedule:list
```

---

## 🎨 Design-System

Alle Views: konsistentes **Dark Violet SaaS**-Theme.

```css
--bg:       #07070f   /* Seitenhintergrund */
--card-bg:  #0f0f1c   /* Cards / Panels */
--accent:   #7c3aed   /* Lavarell-Violett */
--accent-l: #a78bfa   /* Akzent hell */
--text-1:   #f0eeff   /* Primärtext */
--text-2:   #9d9bbf   /* Sekundärtext */
```

**Fonts:** Syne 800 (Headlines) · Inter 400/500/600 (Body)

---

## 📝 .env Pflichtfelder

```env
APP_URL=https://deine-domain.de
APP_KEY=                            # → php artisan key:generate

DB_DATABASE=lavarell
DB_USERNAME=lavarell_user
DB_PASSWORD=sicheres_passwort

MAIL_FROM_ADDRESS=noreply@deine-domain.de
MAIL_FROM_NAME=Lavarell

# PayPal (nach App-Erstellung auf developer.paypal.com)
PAYPAL_MODE=sandbox
PAYPAL_SANDBOX_CLIENT_ID=AX...
PAYPAL_SANDBOX_CLIENT_SECRET=EG...
PAYPAL_WEBHOOK_ID=...

# Nach Billing-Plans erstellen im PayPal-Dashboard:
PAYPAL_PLAN_STARTER_MONTHLY=P-...
PAYPAL_PLAN_STARTER_YEARLY=P-...
PAYPAL_PLAN_PRO_MONTHLY=P-...
PAYPAL_PLAN_PRO_YEARLY=P-...
PAYPAL_PLAN_AGENCY_MONTHLY=P-...
PAYPAL_PLAN_AGENCY_YEARLY=P-...

# Queue & Cache
QUEUE_CONNECTION=redis
CACHE_STORE=redis
SESSION_DRIVER=redis
```

---

## 🔑 Admin-Panel

```
URL:          https://deine-domain.de/admin
Login:        admin@lavarell.com
Passwort:     changeme123!   ← sofort ändern!
```

---

## 📋 PayPal-Einrichtung (Kurzanleitung)

1. **App anlegen:** developer.paypal.com → Apps & Credentials → App erstellen  
2. **Webhook:** URL `https://deine-domain.de/webhooks/paypal` · Events: `BILLING.SUBSCRIPTION.*`, `PAYMENT.SALE.*`  
3. **Billing Plans:** Subscriptions → Plans → je 1× monthly + yearly für Starter/Pro/Agency  
4. Plan-IDs in `.env` → `php artisan db:seed --class=SubscriptionPlanSeeder`

---

## 📜 Lizenz

MIT — © 2025 Lavarell
