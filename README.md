# Fashion Corner (Laravel 12)

Modern ecommerce storefront + admin panel migrated from the legacy Node.js JSON app to **Laravel 12 + MySQL**.

## Requirements

- PHP 8.2+ (Laragon PHP 8.2.29 works; PHP 8.3+ preferred)
- Composer
- MySQL 8
- Apache/Nginx or `php artisan serve`

## Quick start (Laragon)

1. Ensure MySQL is running in Laragon.
2. Database `fashion_corner` is created automatically by setup (or create it manually).
3. From the project root:

```bash
composer install
copy .env.example .env   # if needed
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Or open via Laragon virtual host: `http://fashion-corner.test`

## Default admin login

| Field | Value |
|-------|-------|
| Email | `admin@fashioncorner.test` |
| Password | `password` |

Change this immediately in production.

## Import legacy JSON catalogue

Legacy files live in `_legacy/`:

```bash
php artisan fashion:import-legacy
# or
php artisan fashion:import-legacy --path="D:\path\to\legacy"
```

This imports:

- `_legacy/data/categories.json`
- `_legacy/data/products.json`
- images from `_legacy/public/uploads` → `storage/app/public/products`

`php artisan migrate --seed` already runs the import when `_legacy` is present.

## Key URLs

| Area | URL |
|------|-----|
| Storefront | `/` |
| Shop | `/shop` |
| Product | `/product/{slug}` |
| Cart | `/cart` |
| Checkout (COD) | `/checkout` |
| Admin | `/admin` |
| Login | `/login` |
| Sitemap | `/sitemap.xml` |

## Architecture

- **Eloquent models** with enums for product/order status
- **Form Requests** for validation
- **Policies** for admin authorization
- **Services**: Cart, Orders, Product images, Dashboard, Backup, Legacy import
- **Session cart** with COD checkout + inventory decrement
- **Intervention Image** for resize + thumbnails
- **Bootstrap 5.3** Blade UI (CDN) — no Vite build required for storefront/admin CSS

## Backup / export

Admin → Backup:

- Export products / categories / orders (JSON)
- MySQL dump (falls back to JSON archive if `mysqldump` is unavailable)

## Notes

- Public registration is disabled; only seeded/admin users can access `/admin`.
- Guest checkout is supported via the `customers` table.
- Coupon column exists on orders for future use.
