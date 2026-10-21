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

## Deploy to Laravel Cloud

TrustChain is designed to run as a conventional Laravel application. Laravel Cloud can deploy it directly from this repository and provide the persistent services it needs:

1. Connect this GitHub repository to Laravel Cloud and create an application from the `main` branch.
2. Attach a managed MySQL database and an object storage bucket to the application environment. Cloud supplies their connection settings to the app.
3. Use the `production` environment with `APP_DEBUG=false`. Cloud can generate and manage `APP_KEY` for the environment.
4. Set the build command to `composer install --no-dev && npm ci && npm run build` and the deploy command to `php artisan migrate --force`.
5. Configure a real mail transport before relying on password reset emails.

The evidence upload flow uses Laravel's default filesystem disk, so it works with Cloud object storage when that disk is configured by the environment. Do not run `php artisan db:seed` in production: it creates demo users with the password `password`.
