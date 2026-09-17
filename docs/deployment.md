# Deployment preparation

No deployment target is selected and nothing has been deployed. This repository has a portable local setup; these notes describe runtime requirements rather than claiming every host can run both applications.

| Target | Requirements |
| --- | --- |
| Containers | Separate PHP and Node runtimes, an HTTP server for Laravel's `public/` directory, persistent database/storage, runtime secrets, and worker/scheduler processes if used. Build images in multiple stages; do not include real `.env` files. |
| Bare VPS | PHP 8.3+, Composer, Node 22+, nginx/Apache with PHP-FPM, process supervision for Next.js, database and writable Laravel storage. |
| cPanel/shared hosting | PHP runtime for Laravel with document root pointing to `backend/public`. Next.js requires a supported Node application facility or a separate host; PHP-only plans cannot run its server actions/account flows. |

Preparation commands, run on the chosen environment with its configured database:

```sh
cd backend
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
cd ../frontend
npm ci
npm run build
npm run start
```

`migrate --force` above is an explicit production deployment step for the operator, not part of local setup. Preserve the production application key. Set `APP_ENV=production`, `APP_DEBUG=false`, HTTPS `APP_URL`, secure Laravel session cookies, and a server-only `API_BASE_URL` reachable from the Next.js runtime. HTTPS is required for the frontend's production Secure cookie. No storage symlink is needed for the default private local media disk.

Persist local uploads and SQLite databases across releases, or use a managed SQL database and S3-compatible private storage. Back up the database and media through the chosen hosting process. Preserve old media disk aliases when migrating providers.

Laravel's scheduler should run every minute (`php artisan schedule:run`) for expired-token pruning. Database queue migrations are present; start a queue worker when introducing queued work. This starter does not currently dispatch domain jobs or require Redis/brokers.

If proxy/CDN origins differ from the app origin, configure Next.js Server Action allowed origins narrowly. Multi-instance Next.js deployments need a shared Server Action encryption key and the caching arrangements described in the installed Next.js self-hosting documentation. Set forwarded headers correctly for Laravel URL signing; `APP_URL` must be the externally reachable backend origin.

Run `npm run check` and `npm run build` before release. Verify real database and storage credentials, signed downloads, HTTPS cookies, and the chosen process/HTTP configuration on the target. Provider connectivity and production runtime behavior have not been verified here.
