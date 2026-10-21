# TrustChain

TrustChain is a web app for documenting second-hand device sales and recording a buyer's confirmation of a transaction. Sellers create a record for a device, the buyer reviews it, and confirmation issues a certificate that can be checked through a public verification page.

## What it does

- Create a transaction with a registered buyer, device details, condition notes, agreed price, and accessories.
- Attach optional evidence files (JPEG, PNG, WebP, or PDF; up to 10 MB per file). TrustChain calculates a SHA-256 hash for each uploaded file.
- Let the assigned buyer confirm a pending transaction. Confirmation issues a uniquely numbered certificate with a verification token.
- Let either participant view the transaction and download its evidence. Transaction and certificate records are limited to their seller and buyer.
- Search and filter transaction history, view account activity, and manage a profile.
- Verify a certificate by its number or verification token at `/verify` without signing in.
- Support optional Google Authenticator (TOTP) two-factor authentication for accounts.

## Built with

- PHP 8.2 or newer and Laravel 12
- SQLite by default (MySQL, MariaDB, PostgreSQL, and SQL Server configuration is also present)
- Node.js and npm, with Vite, Tailwind CSS, and Alpine.js for frontend assets

## Run locally

You will need PHP with the extensions required by Laravel, Composer, Node.js, and npm.

```sh
git clone https://github.com/Ashikur14r/trustchain.git
cd trustchain
composer install
cp .env.example .env
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan key:generate
php artisan migrate
npm ci
npm run build
php artisan serve
```

The app will be available at `http://localhost:8000`. For frontend hot reload during development, run `npm run dev` in a separate terminal.

To load example records locally, run `php artisan db:seed`. The seeder creates demo accounts with the password `password`; use this only in a local development database and never seed those accounts into a public or production deployment.

## Configuration notes

Copy `.env.example` to `.env` and set values for the environment where the app runs. Before a production deployment, use a strong `APP_KEY`, set `APP_ENV=production` and `APP_DEBUG=false`, configure a production database, and configure a real mail transport for email verification and password resets. Store uploaded evidence on persistent storage and ensure only the `public/` directory is web-accessible.

Never commit `.env` or production credentials. They are excluded by `.gitignore`.

## Free demo deployment

The repository includes a Docker deployment for Render's free web service. The app uses Supabase's free Postgres database and S3-compatible Storage for durable records and evidence uploads. This setup is intended for a demo: Render may sleep the web service when idle, and Supabase may pause a low-activity free project. Keep independent backups of any important data.

1. Create a free Supabase project. In **Project Settings → Database**, get the connection details and use the session pooler if the direct connection is not reachable from Render. In **Storage**, create a private bucket named `trustchain-evidence` and enable S3 access keys.
2. In Supabase **Storage → S3**, copy the endpoint, region, access key ID, and secret access key. Keep the secret key private.
3. In Render, create a Blueprint from this repository's `render.yaml`. Choose the free plan and Singapore region.
4. Add the Supabase connection values, S3 settings, and a Laravel `APP_KEY` as the environment variables marked as needing a value in Render. Generate a key with `php artisan key:generate --show` and paste it into `APP_KEY`. Set `AWS_BUCKET=trustchain-evidence`. Render runs migrations when the service starts.
5. Set `APP_URL` to the deployed `https://<service>.onrender.com` address. Configure an email provider before relying on verification or password reset emails.

The app stores uploaded evidence on the configured default filesystem disk. Supabase Storage keeps files across Render restarts, while Render's local filesystem is temporary. Do not run `php artisan db:seed` in production: it creates demo users with the password `password`.
