# Leivant Production Launch Checklist

Use this before pointing a real domain to the site.

## 1. Production Environment

- Copy `.env.production.example` to `.env` on the server.
- Set `APP_ENV=production`, `APP_DEBUG=false`, and `APP_URL=https://leivantconstruction.com`.
- Generate a real key with `php artisan key:generate`.
- Set the production database credentials.
- Set `SESSION_ENCRYPT=true`.

## 2. Business Details

- Confirm company name: Leivant Construction Solutions Company.
- Confirm phone: `+255 717 370 799`.
- Confirm email: `info@leivantconstruction.com` or replace it with the final inbox.
- Confirm location: Kinondoni, Dar es Salaam, Tanzania.
- Review `/privacy` and `/terms` with the business owner or legal adviser before launch.

## 3. Payments and Checkout

- Keep `AZAMPAY_MOCK=true` for demos only.
- For real payments, set `AZAMPAY_MOCK=false`.
- Add live AzamPay app name, client ID, client secret, and base URL.
- Place one low-value live order and confirm payment status updates before public launch.

## 4. Products and Images

- Confirm all product names, categories, prices, rental rates, and stock rules in the admin panel.
- Confirm heavy equipment rental rules with the business team.
- Product display images must remain local catalog, storage, or placeholder assets.

## 5. Deployment Commands

Run these on the server after uploading code and configuring `.env`:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
```

Build CSS before upload or on the server:

```bash
.runtime/tools/tailwindcss.exe -c tailwind.standalone.config.cjs -i resources/css/app.css -o public/build/manual/app.css
```

On Linux hosting, use the Linux Tailwind binary or the normal npm build pipeline instead.

## 6. Security and Operations

- Use HTTPS/SSL.
- Keep `/admin` accounts limited and use strong passwords.
- Configure scheduled database backups.
- Configure a queue worker if using database queues.
- Confirm contact form notifications, WhatsApp notifications, and order notifications.
- Check `/up` after deployment for a basic health response.
