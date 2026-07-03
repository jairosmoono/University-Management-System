# University Management System — Installation Guide

## Requirements

| Component | Version |
|-----------|---------|
| PHP | 8.3+ |
| MySQL / MariaDB | 8.0+ |
| Composer | 2.x |
| Node.js / NPM | 18+ (optional, no build step needed) |
| XAMPP / WAMP / Laravel Herd | Any |

---

## Quick Setup (XAMPP on Windows)

### Step 1 — Copy files to htdocs

Place the project folder at:
```
C:\xampp\htdocs\college_management_system\
```

### Step 2 — Create the database

Open **phpMyAdmin** (`http://localhost/phpmyadmin`) and run:

```sql
CREATE DATABASE university_management_system
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
```

### Step 3 — Import the SQL dump

**Option A — phpMyAdmin (recommended):**
1. Select `university_management_system` in phpMyAdmin
2. Click **Import**
3. Choose `university_management_system.sql`
4. Click **Go**

**Option B — Command line:**
```bash
mysql -u root -p university_management_system < university_management_system.sql
```

### Step 4 — Configure environment

```bash
cd C:\xampp\htdocs\college_management_system
copy .env.example .env
```

Edit `.env`:
```ini
APP_NAME="University Management System"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost/college_management_system/public

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=university_management_system
DB_USERNAME=root
DB_PASSWORD=

# Mail (optional — use Mailtrap or SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=noreply@university.com
MAIL_FROM_NAME="University Management System"
```

### Step 5 — Install PHP dependencies

```bash
cd C:\xampp\htdocs\college_management_system
composer install --optimize-autoloader --no-dev
```

### Step 6 — Generate application key

```bash
php artisan key:generate
```

### Step 7 — Run migrations and seed (alternative to SQL import)

If you prefer Laravel migrations instead of the SQL import:
```bash
php artisan migrate --seed
```

### Step 8 — Clear and cache config

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

### Step 9 — Set permissions (Linux/Mac)

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

*On Windows/XAMPP this step is not required.*

---

## Accessing the System

Open your browser:
```
http://localhost/college_management_system/public
```

Or configure a **Virtual Host** in Apache for a cleaner URL.

---

## Default Login Accounts

All accounts use password: **`Admin@123`**

| Role | Email |
|------|-------|
| Super Administrator | admin@university.com |
| Registrar | registrar@university.com |
| Finance Officer | finance@university.com |
| Lecturer | lecturer@university.com |
| Student | student@university.com |
| Librarian | librarian@university.com |
| Hostel Manager | hostel@university.com |
| HR Officer | hr@university.com |
| IT Admin | it@university.com |

---

## Virtual Host Setup (optional, for clean URL)

Edit `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:
```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/college_management_system/public"
    ServerName ums.local
    <Directory "C:/xampp/htdocs/college_management_system/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Edit `C:\Windows\System32\drivers\etc\hosts` (run as Administrator):
```
127.0.0.1   ums.local
```

Restart Apache and visit: `http://ums.local`

---

## Troubleshooting

**500 Server Error**
- Ensure `APP_KEY` is set: `php artisan key:generate`
- Check `storage/logs/laravel.log` for details
- Confirm `storage/` and `bootstrap/cache/` are writable

**Class not found / Composer errors**
```bash
composer dump-autoload
```

**Permission errors on password hash**
- The SQL dump contains bcrypt hashes for `Admin@123`
- If login fails, regenerate the hash:
```bash
php artisan tinker --execute="echo bcrypt('Admin@123');"
```
Then update the `password` field in `users` table in phpMyAdmin.

**Spatie Permission cache**
```bash
php artisan permission:cache-reset
```

**DataTables not loading**
- Ensure you have internet access (Bootstrap, DataTables load from CDN)
- Or download CDN assets locally and update `resources/views/layouts/app.blade.php`

---

## Environment Variables Reference

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_DEBUG` | Show error details | `false` |
| `DB_DATABASE` | MySQL database name | `university_management_system` |
| `FILESYSTEM_DISK` | Where uploads are stored | `local` |
| `MAIL_MAILER` | Email driver | `log` |
| `SESSION_DRIVER` | Session storage | `file` |
| `CACHE_STORE` | Cache driver | `file` |

---

## Production Deployment Checklist

- [ ] Set `APP_ENV=production` and `APP_DEBUG=false`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Set strong `APP_KEY`
- [ ] Configure real SMTP mail settings
- [ ] Set up HTTPS (SSL certificate)
- [ ] Configure cron for scheduled tasks: `php artisan schedule:run`
- [ ] Set up queue worker if using queues: `php artisan queue:work`
- [ ] Restrict database user permissions (no root in production)
- [ ] Set storage permissions: `chmod -R 775 storage`

---

## Module Summary

| Module | Description |
|--------|-------------|
| Students | Enrollment, profile management, ID cards |
| Admissions | Applications, approval workflow |
| Academic | Faculties, departments, programs, courses, timetables |
| Results | Grades, GPA/CGPA calculation, transcripts |
| Finance | Billing, payments (Airtel/MTN/Zamtel/Visa/Cash), scholarships |
| Hostel | Accommodation, room allocations |
| Library | Books, borrowings, overdue fines |
| HR | Employees, leave management, payroll, payslips |
| Assets | University asset register |
| Research | Projects, publications |
| Announcements | Campus-wide notices |
| Messages | Internal messaging |
| Documents | File uploads and management |
| Support | Helpdesk ticketing system |
| Alumni | Graduate directory and tracking |
| Reports | Academic, finance, HR, library reports with PDF export |
| Admin | User management, roles, permissions, settings, audit logs |

---

*Built with Laravel 12 · PHP 8.3 · MySQL 8 · Bootstrap 5 · Spatie Laravel Permission*
