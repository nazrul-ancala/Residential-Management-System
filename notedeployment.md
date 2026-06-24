# Render + Clever Cloud Deployment Guide

Deploy two Laravel apps (frontend + API) using Docker on Render with MySQL on Clever Cloud.
Learned from CWMS project. Follow this exactly for future projects.

---

## Architecture

```
Browser
  │
  ├── your-app   (Render free tier, Docker)  ← Blade UI, session auth
  │     └── calls your-api via HTTP Basic Auth
  │
  └── your-api   (Render free tier, Docker)  ← REST API, all business logic
        └── MySQL on Clever Cloud (external, free tier)
```

Render does NOT offer MySQL. Use Clever Cloud as an external MySQL provider.
Both Laravel apps connect to the SAME Clever Cloud database.

---

## Step 1 — Set up MySQL on Clever Cloud

1. Go to https://console.clever-cloud.com
2. Create account → **Create** → **an add-on** → **MySQL**
3. Choose **DEV** plan (free, 5 max connections — important later)
4. After creation, go to add-on → **Information** tab
5. Save these values:

| Clever Cloud key | Your .env key |
|---|---|
| `MYSQL_ADDON_HOST` | `DB_HOST` |
| `MYSQL_ADDON_PORT` | `DB_PORT` |
| `MYSQL_ADDON_DB` | `DB_DATABASE` |
| `MYSQL_ADDON_USER` | `DB_USERNAME` |
| `MYSQL_ADDON_PASSWORD` | `DB_PASSWORD` |

6. Import your SQL (schema + stored procedures):
   - Go to add-on → **PHPMyAdmin** button (easiest way in)
   - Clever Cloud blocks direct external connections by default

> **Warning:** Clever Cloud free MySQL allows max **5 simultaneous connections** across ALL your apps.
> Exceed this → `SQLSTATE[HY000] [1226] User '...' has exceeded the 'max_user_connections'`

---

## Step 2 — Dockerfiles

Each Laravel app needs its own `Dockerfile.fly` in its root folder.

**API app** (no Node/npm):
```dockerfile
FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    nginx supervisor \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

RUN composer install --optimize-autoloader --no-dev \
    && php artisan package:discover --ansi || true \
    && rm -f bootstrap/cache/services.php bootstrap/cache/packages.php

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

COPY docker/php-fpm-pool.conf /usr/local/etc/php-fpm.d/www.conf

RUN rm -f /etc/nginx/sites-enabled/default
COPY docker/fly-nginx.conf /etc/nginx/sites-available/app
RUN ln -s /etc/nginx/sites-available/app /etc/nginx/sites-enabled/app
COPY docker/fly-supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/fly-entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 8080
ENTRYPOINT ["/entrypoint.sh"]
```

**Frontend app** (needs Node for Vite):
```dockerfile
FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    nginx supervisor \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev nodejs npm \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

RUN composer install --optimize-autoloader --no-dev \
    && rm -f bootstrap/cache/services.php bootstrap/cache/packages.php

ARG VITE_API_URL=http://localhost:8001
ENV VITE_API_URL=$VITE_API_URL
RUN npm install && npm run build

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

COPY docker/php-fpm-pool.conf /usr/local/etc/php-fpm.d/www.conf

RUN rm -f /etc/nginx/sites-enabled/default
COPY docker/fly-nginx.conf /etc/nginx/sites-available/app
RUN ln -s /etc/nginx/sites-available/app /etc/nginx/sites-enabled/app
COPY docker/fly-supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/fly-entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 8080
ENTRYPOINT ["/entrypoint.sh"]
```

---

## Step 3 — PHP-FPM Pool Config (CRITICAL)

**This is the most important step. Skip it and you will get `max_user_connections` errors.**

Create `docker/php-fpm-pool.conf` inside EACH app folder (same content for both):

```ini
[www]
user = www-data
group = www-data
listen = 127.0.0.1:9000

; Both apps share Clever Cloud's 5-connection limit.
; 2 workers each = 4 connections max = safe under the limit of 5.
pm = dynamic
pm.max_children = 2
pm.start_servers = 1
pm.min_spare_servers = 1
pm.max_spare_servers = 2
pm.max_requests = 500
```

**Why this matters:**
- Each PHP-FPM worker opens 1 MySQL connection
- Default PHP-FPM = 5 workers per app
- 2 apps × 5 workers = 10 connections → crashes Clever Cloud free tier
- 2 apps × 2 workers = 4 connections → safe

---

## Step 4 — Docker support files

Each app needs a `docker/` folder with 3 files:

**`docker/fly-nginx.conf`:**
```nginx
server {
    listen 8080;
    server_name _;
    root /var/www/public;
    index index.php;

    client_max_body_size 50M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

**`docker/fly-supervisord.conf`:**
```ini
[supervisord]
nodaemon=true
logfile=/dev/null
logfile_maxbytes=0
pidfile=/tmp/supervisord.pid

[program:php-fpm]
command=/usr/local/sbin/php-fpm -F
autostart=true
autorestart=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0

[program:nginx]
command=/usr/sbin/nginx -g "daemon off;"
autostart=true
autorestart=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
```

**`docker/fly-entrypoint.sh`:**
```bash
#!/bin/bash
set -e

cat > /var/www/.env << EOF
APP_NAME=Laravel
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL}

DB_CONNECTION=${DB_CONNECTION:-mysql}
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}

SESSION_DRIVER=${SESSION_DRIVER:-cookie}
CACHE_STORE=${CACHE_STORE:-file}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-sync}
LOG_CHANNEL=${LOG_CHANNEL:-stderr}
EOF

php /var/www/artisan config:clear
php /var/www/artisan config:cache

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
```

> Add any extra env vars your app needs to the heredoc block above (tokens, API keys, etc.)

---

## Step 5 — render.yaml

Put in project root. Use `sync: false` for ALL secrets — never hardcode credentials in this file.

```yaml
services:
  - type: web
    name: your-api
    runtime: docker
    dockerfilePath: ./your-api/Dockerfile.fly
    dockerContext: ./your-api
    plan: free
    envVars:
      - key: APP_ENV
        value: production
      - key: APP_DEBUG
        value: "false"
      - key: APP_URL
        value: https://your-api.onrender.com
      - key: APP_KEY
        sync: false
      - key: DB_CONNECTION
        value: mysql
      - key: DB_HOST
        sync: false
      - key: DB_PORT
        sync: false
      - key: DB_DATABASE
        sync: false
      - key: DB_USERNAME
        sync: false
      - key: DB_PASSWORD
        sync: false
      - key: SESSION_DRIVER
        value: cookie
      - key: CACHE_STORE
        value: file
      - key: QUEUE_CONNECTION
        value: sync
      - key: LOG_CHANNEL
        value: stderr

  - type: web
    name: your-app
    runtime: docker
    dockerfilePath: ./your-app/Dockerfile.fly
    dockerContext: ./your-app
    plan: free
    envVars:
      - key: APP_ENV
        value: production
      - key: APP_DEBUG
        value: "false"
      - key: APP_URL
        value: https://your-app.onrender.com
      - key: APP_KEY
        sync: false
      - key: DB_CONNECTION
        value: mysql
      - key: DB_HOST
        sync: false
      - key: DB_PORT
        sync: false
      - key: DB_DATABASE
        sync: false
      - key: DB_USERNAME
        sync: false
      - key: DB_PASSWORD
        sync: false
      - key: SESSION_DRIVER
        value: cookie
      - key: CACHE_STORE
        value: file
      - key: QUEUE_CONNECTION
        value: sync
      - key: LOG_CHANNEL
        value: stderr
      - key: VITE_API_URL
        value: https://your-api.onrender.com
```

**Rule:** `value:` for non-sensitive config. `sync: false` for anything secret.
`sync: false` = Render asks you to type the value in the dashboard — it never goes in git.

---

## Step 6 — Deploy on Render

1. Push all code to GitHub (make sure `.env` is gitignored)
2. Go to https://dashboard.render.com → **Blueprints** → **+ New Blueprint Instance**
3. Connect GitHub repo → select branch `main`
4. Render reads `render.yaml` → creates both services
5. For every `sync: false` variable, Render shows a form — type in the real values
6. Click **Apply** → Render builds Docker images and deploys (~5-15 min)

**Have these ready when Render asks:**
- `APP_KEY` → run `php artisan key:generate --show` locally
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` → from Clever Cloud Information tab
- Any other secrets your app uses

---

## Step 7 — Health endpoint

Add to your API `routes/api.php` before any middleware group:

```php
Route::get('/health', fn() => response()->json(['status' => 'ok']));
```

UptimeRobot will ping this. No DB query, no auth = fast and cheap.

---

## Step 8 — UptimeRobot (keep services alive)

Render free tier spins down after 15 min idle → cold start takes 50+ seconds.
UptimeRobot pings reduce how often this happens.

1. Go to https://uptimerobot.com → free account
2. **+ Add New Monitor** → API:
   - Type: `HTTP(s)`, URL: `https://your-api.onrender.com/api/health`, Interval: `5 min`
3. **+ Add New Monitor** → Frontend:
   - Type: `HTTP(s)`, URL: `https://your-app.onrender.com`, Interval: `5 min`

> Note: Render may still spin down despite pings. Cold starts on free tier are unavoidable,
> just less frequent. Upgrade to paid ($7/mo) to eliminate cold starts entirely.

---

## Troubleshooting

| Error | Cause | Fix |
|---|---|---|
| `SQLSTATE[HY000] [1226] max_user_connections` | Too many PHP-FPM workers | Add `php-fpm-pool.conf` with `pm.max_children = 2` to BOTH apps |
| `502 Bad Gateway` (first request) | Cold start — service spinning up | Wait 50 sec and refresh |
| `502` persists after 5+ min | Container crashing on startup | Render → service → Logs → look for PHP/Laravel error |
| Old code showing after push | Render cached Docker layers | Render → service → **Manual Deploy → Clear build cache & deploy** |
| Can't connect to Clever Cloud from local MySQL Workbench | Clever Cloud blocks external IPs | Use PHPMyAdmin via Clever Cloud console instead |
| `SIGQUIT` in Render logs every 15 min | Render free tier spin-down | Normal. UptimeRobot reduces frequency |
| Only one page fails, others work | That page makes more concurrent API calls | Check if it fires multiple AJAX requests simultaneously — hits connection limit |

---

## .gitignore checklist

```gitignore
.env
/database/stored_procedures/
/database/schema/
```

## Security checklist before making repo public

- [ ] `.env` is gitignored (check with `git status`)
- [ ] `render.yaml` uses `sync: false` for ALL secrets (no hardcoded passwords/keys)
- [ ] No credentials in `.md` files or docs
- [ ] Stored procedures folder is gitignored

---

## File structure summary

```
project-root/
├── render.yaml
├── your-api/
│   ├── Dockerfile.fly
│   └── docker/
│       ├── fly-nginx.conf
│       ├── fly-supervisord.conf
│       ├── fly-entrypoint.sh
│       └── php-fpm-pool.conf      ← CRITICAL
└── your-app/
    ├── Dockerfile.fly
    └── docker/
        ├── fly-nginx.conf
        ├── fly-supervisord.conf
        ├── fly-entrypoint.sh
        └── php-fpm-pool.conf      ← CRITICAL
```
