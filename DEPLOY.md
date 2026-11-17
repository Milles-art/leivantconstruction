# Leivant — cPanel deployment guide

## Files prepared

| File | Contents |
|---|---|
| `deploy/leivant-app.zip` | Laravel application (code + `vendor/`, so Composer is NOT needed on the server) |
| `deploy/public_html.zip` | Document root: mirrored CSS/JS/fonts/images, `index.php`, `.htaccess` |
| `leivant-app/.env.production.ready` | Production `.env` with a generated `APP_KEY` |

## 1. cPanel preparation

1. Log in to cPanel (`https://www.leivantconstruction.com:2083/`).
2. **PHP version**: set to **8.2, 8.3 or 8.4** (Select PHP Version / PHP Selector).
3. **MySQL database**: create a database and a user, then add the user to the database with **ALL PRIVILEGES**.
   Note the exact DB name, username and password — you will paste them into `.env`.
4. **SSL**: install/activate the SSL certificate for the domain and force HTTPS.

## 2. Upload

1. Open **File Manager** → go to your home directory (above `public_html`).
2. Upload `deploy/leivant-app.zip`, extract it, then **rename** the extracted folder to `leivant-app`.
   - Final path should be: `/home/<cpanel-user>/leivant-app/artisan`
3. Go into `public_html`, upload `deploy/public_html.zip`, extract it **directly inside `public_html`** (not into a subfolder).
   - Final path should be: `/home/<cpanel-user>/public_html/index.php`
   - `public_html/index.php` already points to `../leivant-app/bootstrap/app.php`, so the app and webroot stay separate.

## 3. Environment file

1. In File Manager, open `leivant-app/.env.production.ready`, copy its contents.
2. Create `leivant-app/.env` and paste them.
3. Replace these values:
   - `DB_DATABASE` → the database name from step 1
   - `DB_USERNAME` → the database user
   - `DB_PASSWORD` → that user's password
   - `APP_URL` → the real domain (`https://leivantconstruction.com`)
   - `SESSION_DOMAIN` → `.leivantconstruction.com` (or remove it for a staging domain)
   - `MAIL_PASSWORD` → mailbox password (leave blank if mail is not configured yet)
   - `ADMIN_PASSWORD` → a strong admin password before any seeding
   - payment/API keys (AzamPay, WhatsApp) when available; keep `AZAMPAY_MOCK=true` for testing
4. Save.

## 4. Permissions

In File Manager (or via SSH), set:

- `leivant-app/storage` and everything inside → **755** (775 if the PHP user is different from the owner)
- `leivant-app/bootstrap/cache` → **755** (775)
- `public_html` → **755**
- files → **644**

## 5. Deployment commands (SSH or cPanel Terminal)

```bash
cd ~/leivant-app
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize:clear   # only if something errors, then re-cache
```

If `storage:link` complains that the link already exists (the webroot was mirrored with a `public` symlink locally), ignore it — production uses `public_html/` directly.

If cPanel has no Terminal/SSH, tell me and I will prepare a one-time, password-protected migration runner instead.

## 6. Verify

Open these URLs and confirm each loads with the new design:

- `/` (homepage)
- `/services`, `/projects`, `/products`, `/discovery`, `/about`, `/contact`
- `/cart`, `/login`
- `/up` (health check)
- `/sitemap.xml`

Also confirm:
- the official logo shows in the header and footer
- all seven nav links navigate to separate pages
- no "Something went wrong" error appears
- images, fonts and CSS load locally (no external hercules-cdn.com requests)

## 7. After launch

- Change the cPanel password that was shared in chat.
- Keep `/admin` accounts limited and use strong passwords.
- Set up scheduled backups of the database and `public_html`.
- If using database queues, configure a queue worker or cron.
