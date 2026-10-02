# Leivant Construction Solutions

Laravel 11 + MySQL marketplace scaffold for Leivant Construction Solutions Company in Dar es Salaam, Tanzania.

## Local setup

1. Install PHP 8.2+, Composer, Node.js, npm, and MySQL.
2. Copy `.env.example` to `.env` and set database credentials.
3. Run `composer install`.
4. Run `npm install`.
5. Run `php artisan key:generate`.
6. Run `php artisan storage:link`.
7. Run `php artisan migrate --seed`.
8. Run `composer run dev`.

The first admin user is seeded from `ADMIN_NAME`, `ADMIN_EMAIL`, and `ADMIN_PASSWORD`.

## Production launch

Use `.env.production.example` as the server environment template and follow `docs/LAUNCH_CHECKLIST.md`.

Before launch, confirm real payment credentials, mail credentials, final business contact details, product prices, product availability, privacy text, and terms text.
