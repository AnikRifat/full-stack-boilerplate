# Laravel + Livewire + Next.js Starter

A generic full stack boilerplate. Laravel owns the Livewire admin, API, database, authorization, and media storage. Next.js provides the public frontend and account pages.

| Core feature | Included |
| --- | --- |
| Administration | Session login/logout, dashboard, responsive layouts and reusable form components |
| Users and employees | Accounts, activation, employee codes and profiles |
| Roles and permissions | Shoplagbe-style config system roles, custom roles, extra system roles and personal denials |
| Application settings | App name, support email and public registration switch |
| Shared media | One service for admin/API uploads, file tracking, replacement, deletion and signed downloads |
| Frontend | Registration, login, profile, file upload/list/delete and failure/loading states |
| API | Versioned `/api/v1` auth, profile, public configuration and media endpoints |

No commerce, logistics, payments, tenancy, or other domain modules are included. The source SawdahExpress project is unchanged.

## Requirements

- PHP 8.3+ with Composer 2 and Laravel's required extensions; GD is needed for image test fixtures.
- Node.js 22+ and npm. The installed Next.js version supports Node 20.9+, but this starter uses Node 22+ as its project baseline.
- SQLite for the default local setup. MySQL/PostgreSQL can be selected in Laravel's `.env`.

Dependencies are locked. Versions at creation: Laravel 13.32, Livewire 4.4, Sanctum 4.3, Next.js 16.3, React 19.2. Composer resolves against PHP 8.3 to avoid locking PHP 8.4-only dependencies on a newer developer machine.

## Start locally

From the repository root:

```sh
npm run setup
cd backend
php artisan app:create-admin
cd ..
npm run dev
```

`setup` installs locked dependencies, creates missing local environment files, preserves an existing application key, migrates the local database, and builds both applications. It requires Composer, PHP, Node and npm already on your machine. `app:create-admin` asks for a name, email, and password without echoing the password. There is no known default administrator password.

- Frontend: http://127.0.0.1:3000
- Admin: http://127.0.0.1:8000/admin
- API health: http://127.0.0.1:8000/api/v1/health

### Try the demo data

```sh
npm run demo
```

Seeds one account per role (owner, administrator, employee, a custom `support-agent`, member, plus a suspended account) and prints a freshly generated shared password once — nothing is hardcoded, so re-run it whenever you need the password again. Local-only: the seeder refuses to run when `APP_ENV=production`. Delete `backend/database/seeders/DemoSeeder.php` and the `demo` script when you start a real project.

Use `127.0.0.1` consistently. The root `dev` command starts Laravel, Vite and Next.js together. Existing projects can use `composer install` and `npm ci` in each app; you do not need to rerun setup to develop.

## Checks

```sh
npm run check    # Frontend lint/types and backend format/tests
npm run build    # Laravel admin assets and Next.js production build
npm run test     # Backend behavior suite, isolated SQLite
```

Next.js production builds do not require a running backend. Runtime pages read public application settings from Laravel. The API URL is server-only (`frontend/.env.local` → `API_BASE_URL`); tokens never enter browser JavaScript or public environment variables.

## Storage

Default uploads use Laravel's private `local` disk. Set `MEDIA_DISK=s3` in `backend/.env` and fill the existing AWS configuration to use AWS S3 or an S3-compatible provider. `AWS_ENDPOINT` and `AWS_USE_PATH_STYLE_ENDPOINT` support alternative providers; use the values documented by your provider. Files retain the disk chosen when uploaded. There is no automatic copying of old files when storage configuration changes.

See [media documentation](docs/media.md), [permission rules](docs/permissions.md), [API contract](docs/api.md), [architecture](docs/architecture.md), [deployment preparation](docs/deployment.md), and [source scan](docs/source-scan.md).

Deploying the full stack requires both PHP and Node capabilities. PHP-only shared hosting can run the backend while Next.js runs on another host. Production deployment and live S3 verification are outside the completed local checks.
