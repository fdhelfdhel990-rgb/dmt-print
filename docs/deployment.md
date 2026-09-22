# DMT Print Deployment

## Checklist

1. Set production environment values in `.env`, including `APP_ENV=production`, `APP_DEBUG=false`, database credentials, mail settings, and `APP_URL`.
2. Install dependencies with production flags:
   - `composer install --no-dev --optimize-autoloader`
   - `npm ci`
   - `npm run build`
3. Run database migrations:
   - `php artisan migrate --force`
4. Prepare Laravel caches:
   - `php artisan config:cache`
   - `php artisan route:cache`
   - `php artisan view:cache`
5. Link storage when public storage is used:
   - `php artisan storage:link`
6. Make sure `storage/` and `bootstrap/cache/` are writable by the web server.
7. Configure a queue worker if queued jobs are introduced later.
8. Configure the scheduler with `php artisan schedule:run` every minute if scheduled tasks are introduced later.

## Do Not Deploy

Do not publish local `.env`, local databases, uploaded payment proofs, `storage/logs`, `storage/framework/cache`, `.codex*`, `.agents`, `vendor`, or `node_modules`.

## Smoke Test

After deployment, verify:

- Customer home, catalog, cart, checkout, and order tracking pages load.
- Admin can log in and access dashboard, orders, products, stock, cashbook, appearance, and settings.
- `php artisan route:list --except-vendor` completes without errors.
