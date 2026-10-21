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

## Backend container (Dokploy or any Docker host)

`backend/Dockerfile` builds the Laravel backend only: assets are built with Node in a first stage, then served by `serversideup/php` (nginx + PHP-FPM) on port 8080. On every container start it waits for the database, runs pending migrations, links storage and caches config, routes, views and events. Run a single replica, since migrations run on start. The Next.js frontend is a separate service.

1. Create a MySQL 8 or PostgreSQL database and note its internal host and credentials.
2. Create an application from this repository with build type **Dockerfile**, build context `backend`, Dockerfile `backend/Dockerfile`.
3. Set the environment. Generate the key once with `php artisan key:generate --show` and keep it:
   ```ini
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=base64:...
   APP_URL=https://your-backend-domain
   TRUSTED_PROXIES=*
   LOG_CHANNEL=stderr
   LOG_LEVEL=error
   DB_CONNECTION=mysql
   DB_HOST=...
   DB_PORT=3306
   DB_DATABASE=...
   DB_USERNAME=...
   DB_PASSWORD=...
   SESSION_SECURE_COOKIE=true
   ```
4. Mount a persistent volume at `/var/www/html/storage/app`, or use S3-compatible media storage. Local uploads are lost on every deploy without it.
5. Route the domain to container port 8080 with HTTPS.
6. Deploy, then create the super admin once from the container terminal: `php artisan app:create-admin`.

`TRUSTED_PROXIES=*` is only safe when the container is reachable solely through the proxy; otherwise list the proxy addresses. In production, generated URLs are forced to HTTPS.

## Operations

Laravel's scheduler should run every minute (`php artisan schedule:run`) for expired-token pruning. Database queue migrations are present; start a queue worker when introducing queued work. This starter does not currently dispatch domain jobs or require Redis/brokers.

If proxy/CDN origins differ from the app origin, configure Next.js Server Action allowed origins narrowly. Multi-instance Next.js deployments need a shared Server Action encryption key and the caching arrangements described in the installed Next.js self-hosting documentation. Set forwarded headers correctly for Laravel URL signing; `APP_URL` must be the externally reachable backend origin.

Run `npm run check` and `npm run build` before release. Verify real database and storage credentials, signed downloads, HTTPS cookies, and the chosen process/HTTP configuration on the target. Provider connectivity and production runtime behavior have not been verified here.
