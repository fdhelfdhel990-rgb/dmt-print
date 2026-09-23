# DMT Print Deployment

## Production Environment

- Set `APP_ENV=production`, `APP_DEBUG=false`, and `APP_URL=https://domain-anda`.
- Use MySQL/MariaDB with `DB_CONNECTION=mysql`; do not deploy SQLite development files.
- Use `SESSION_DRIVER=database`, plus `CACHE_STORE=database` or Redis when available.
- Keep all secrets in environment variables only: database credentials, admin seed password, mail credentials, and object storage keys.
- Set `DMT_ADMIN_PASSWORD` only during initial seeding or a controlled admin reset.

## Filesystem

Public uploads use the Laravel `public` disk:

- `banners/`
- `products/`
- `categories/`
- `payment-methods/`

Private uploads use the Laravel `local` disk:

- `order-designs/`
- `payment-proofs/`

For local deployment, keep:

```env
PUBLIC_FILESYSTEM_DRIVER=local
PRIVATE_FILESYSTEM_DRIVER=local
```

Then run:

```bash
php artisan storage:link
```

For R2/S3-compatible storage, set the public and private disk driver to `s3` and fill only environment credentials. Use separate buckets or prefixes for public and private files when possible. Do not make payment proofs or customer designs public.

## Deployment Commands

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan optimize
```

Run `php artisan storage:link` only when the public disk is local.

## Security Checklist

- Admin routes are under `auth` and `admin` middleware; inactive admins receive 403.
- Admin login is throttled.
- Checkout is throttled and CSRF-protected.
- Payment proof download/preview routes are admin-only and stream private files through authorization.
- Upload validation restricts MIME type and size.
- Payment proofs and customer design files are not exposed via public URLs.
- Keep `APP_DEBUG=false` in production.
- Confirm there are no debug routes, hard-coded passwords, `.env` files, uploaded files, logs, `vendor`, or `node_modules` in Git.

## Database And Operations

- Migrations are MySQL-compatible and should run on an empty database with `php artisan migrate --force`.
- Financial state changes use database transactions.
- Verified payment totals are derived from payment records and order snapshots.
- Stock deduction remains guarded against duplicate deduction.
- Archive keeps order relations intact.
- Back up MySQL before every production migration.
- Back up public and private object storage with retention appropriate for customer orders and payment evidence.

## Rollback And Maintenance

```bash
php artisan down
git pull --ff-only
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan optimize
php artisan up
```

For rollback, deploy the previous Git release and run only reversible migrations after reviewing data impact. Never run `migrate:fresh` on production or development data.

## Smoke Test

- Open `/up`.
- Open homepage, catalog, product detail, cart, checkout, order success, and tracking.
- Upload a product image and confirm it appears across catalog, detail, cart, checkout, tracking, admin order detail, and invoice.
- Upload a payment proof as a customer, then confirm an active admin can preview/download it and a guest cannot.
- Open `Cetak Invoice`, check A4 print preview, then save as PDF from the browser.
- Run `php artisan route:list --except-vendor` and confirm no duplicate or unexpected debug route.

## UAT Checklist

- Category filtering and pagination keep `kategori`.
- Two homepage banners rotate and fall back cleanly.
- Instagram/TikTok links are driven from settings.
- Payment methods only appear when active and complete.
- Cart badge follows session quantity.
- Order item options appear as text snapshots after checkout, never editable configurators.
- Invoice totals, paid amount, and remaining balance match the admin order detail.
