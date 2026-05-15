# Calculator System

A simple Laravel calculator app with:

- Calculator page with template selector
- Template CRUD
- Calculation history
- Formula: `width * height * amount * multiplier`
- White, yellow, and black Tailwind UI

## Setup

```bash
cd /home/azzatulzulkarnain/Development/calculator
composer install
npm install
cp .env.example .env
php artisan key:generate
```

This project is configured for MySQL by default:

```env
DB_CONNECTION=mysql
DB_HOST=172.20.0.4
DB_PORT=3306
DB_DATABASE=calculator
DB_USERNAME=root
DB_PASSWORD=mysqlroot
```

Make sure MySQL is running, create the database, and make sure PHP has the MySQL PDO driver installed and enabled. On Ubuntu/Debian this is usually:

```bash
docker exec rds_master mysql -uroot -pmysqlroot -e "CREATE DATABASE IF NOT EXISTS calculator CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Then run:

```bash
php artisan migrate --seed
npm run build
php artisan serve --host=127.0.0.1 --port=8086
```

Open:

```text
http://127.0.0.1:8086
```

## SQLite Alternative

If you prefer SQLite, update `.env`:

```env
DB_CONNECTION=sqlite
```

Then make sure `php-sqlite3` is installed and run:

```bash
touch database/database.sqlite
php artisan migrate --seed
```

## Main Files

- Routes: `routes/web.php`
- Controllers: `app/Http/Controllers`
- Models: `app/Models/CalculationTemplate.php`, `app/Models/CalculationHistory.php`
- Migrations: `database/migrations`
- Views: `resources/views`
- Styling: `resources/css/app.css`
