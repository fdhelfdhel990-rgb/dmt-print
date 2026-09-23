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

## Local MySQL Setup

1. Create a database named `dmt_print` with `utf8mb4` charset and an InnoDB-capable server.
2. Copy `.env.example` to `.env`, then fill `DB_CONNECTION=mysql`, host, port, database, username, and password.
3. Run `php artisan migrate`.
4. Run `php artisan db:seed` when seed data is needed.
5. Run `php artisan storage:link` so banner, product, category, and QRIS images can be served.
6. Start the app with `composer run dev` or the local server command used by the team.
7. Back up MySQL before changing production schema or importing data.

## MySQL Test Database

Use a separate database such as `dmt_print_test` for migration and feature-test checks. Do not run destructive migration commands against a development or production database that contains real data.
