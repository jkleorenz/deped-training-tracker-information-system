# DepEd Training Tracker Information System — Setup & Deployment Guide

## Folder structure (deliverables)

```
app/
├── Exports/
│   └── TrainingsExport.php
├── Http/
│   ├── Controllers/
│   │   ├── Auth/AuthController.php
│   │   ├── DashboardController.php
│   │   ├── PersonnelController.php
│   │   ├── ReportController.php
│   │   └── TrainingController.php
│   └── Middleware/
│       └── CheckRole.php
├── Models/
│   ├── Training.php
│   ├── User.php
│   └── UserTraining.php
└── Policies/
    └── UserPolicy.php
config/           (app.php uses APP_NAME, APP_URL from .env)
database/
├── migrations/  (users, add_personnel_fields, trainings, user_trainings)
└── seeders/
    └── DatabaseSeeder.php
resources/views/
├── auth/         (login, register)
├── dashboard/    (admin, personnel)
├── layouts/      (app.blade.php)
├── personnel/    (index)
├── reports/      (trainings-pdf.blade.php)
└── trainings/    (index)
routes/
└── web.php       (auth, dashboard, personnel, api/trainings, reports)
```

---

This guide covers setup for **intranet (XAMPP/Laragon, LAN)** and **production (shared/VPS hosting)**. The same codebase runs in both environments with no code changes; only configuration differs.

---

## 1. Requirements

- PHP 8.1 or higher (with extensions: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML)
- Composer
- MySQL 5.7+ or MariaDB
- Web server (Apache with mod_rewrite, or Nginx)

---

## 2. Intranet Setup (XAMPP / Laragon, Offline LAN)

### 2.1 Install the application

```bash
# Clone or copy the project to your web root, e.g.:
# XAMPP: C:\xampp\htdocs\dashboard\DepEd-Training-Tracker-Information-System
# Laragon: C:\laragon\www\DepEd-Training-Tracker-Information-System

cd path/to/DepEd-Training-Tracker-Information-System
```

### 2.2 Install dependencies

```bash
composer install --no-dev --optimize-autoloader
```

(Use `composer install` if you need dev tools.)

### 2.3 Environment configuration

```bash
# Copy environment file
copy .env.example .env

# Generate application key
php artisan key:generate
```

Edit `.env`:

```env
APP_NAME="DepEd Training Tracker Information System"
APP_ENV=local
APP_DEBUG=true
# Use the LAN IP of this machine so other PCs can open the app (e.g. http://192.168.1.100)
# Or use http://localhost if only this PC will use it
APP_URL=http://192.168.1.100

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=deped_training_tracker
DB_USERNAME=root
DB_PASSWORD=
```

Create the database in MySQL (phpMyAdmin or command line):

```sql
CREATE DATABASE deped_training_tracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2.4 Run migrations and seed (optional)

```bash
php artisan migrate
php artisan db:seed
```

After seeding, you can log in with:

- **Admin:** `admin@deped.local` / `password`
- **Personnel:** `juan.delacruz@deped.local` or `maria.santos@deped.local` / `password`

### 2.5 Access via LAN

1. Find this PC’s LAN IP (e.g. `192.168.1.100`).
2. Set `APP_URL` in `.env` to `http://192.168.1.100` (or with a subfolder if applicable).
3. Ensure Apache/Nginx and MySQL are running.
4. From other PCs on the LAN, open: `http://192.168.1.100/` (or your project subfolder, e.g. `http://192.168.1.100/dashboard/DepEd-Training-Tracker-Information-System/public`).

**XAMPP subfolder:** If the app is in `htdocs/dashboard/DepEd-Training-Tracker-Information-System`, either:

- Use a virtual host pointing document root to `.../DepEd-Training-Tracker-Information-System/public`, or  
- Access as `http://192.168.1.100/dashboard/DepEd-Training-Tracker-Information-System/public` and set `APP_URL` to that full URL.

**Offline use:** The app uses Bootstrap and Bootstrap Icons from CDN. For fully offline use, download Bootstrap 5 and Bootstrap Icons and serve them from `/public/css` and `/public/js` (or use a local copy and update the layout Blade templates).

---

## 3. Production Deployment (Shared / VPS Hosting)

### 3.1 Upload code

Upload the project (e.g. via Git, FTP, or SCP). Do **not** upload `.env`; create it on the server from `.env.example`.

### 3.2 Server configuration

**Document root must point to the `public` directory:**

- **Apache:** Set `DocumentRoot` to `.../public`. Ensure `mod_rewrite` is enabled and `.htaccess` is allowed.
- **Nginx:** Set `root` to `.../public` and use a standard Laravel `try_files` block:

```nginx
root /path/to/DepEd-Training-Tracker-Information-System/public;
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 3.3 Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` for production:

```env
APP_NAME="DepEd Training Tracker Information System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.gov.ph

DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

### 3.4 Migrate and optional seed

```bash
php artisan migrate --force
# php artisan db:seed --force   # only if you need seed data
```

### 3.5 Permissions and optimization

```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache   # Linux; adjust user/group as needed

php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3.6 HTTPS

Use your host’s or reverse proxy’s SSL (e.g. Let’s Encrypt). Set `APP_URL` to `https://...` and ensure `APP_DEBUG=false` in production.

---

## 4. No Hard-Coded URLs

The application uses:

- `config('app.name')` and `config('app.url')` from `.env`
- `route()`, `url()`, `asset()` for links and assets

Changing `APP_URL` and (if used) `ASSET_URL` in `.env` is sufficient for switching between intranet and production; no code changes are required.

---

## 5. Default Credentials (after seeding)

| Role      | Email                     | Password  |
|----------|----------------------------|-----------|
| Admin    | admin@deped.local          | password  |
| Personnel| juan.delacruz@deped.local  | password  |
| Personnel| maria.santos@deped.local  | password  |

Change these immediately in production.

---

## 6. Troubleshooting

- **500 error:** Check `storage/logs/laravel.log`; ensure `storage` and `bootstrap/cache` are writable.
- **Login redirect / session:** Ensure `APP_URL` matches the URL you use in the browser; clear browser cache/cookies.
- **CSRF / 419:** Confirm session driver and that cookies are allowed; in production use HTTPS and correct `APP_URL`.
- **DB connection:** Verify `DB_*` in `.env` and that the database exists and the user has privileges.
