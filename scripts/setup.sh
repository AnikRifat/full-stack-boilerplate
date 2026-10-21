#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."
composer --working-dir=backend install --no-interaction
npm --prefix backend ci
npm --prefix frontend ci
if [ ! -f backend/.env ]; then cp backend/.env.example backend/.env; fi
if [ ! -f frontend/.env.local ]; then cp frontend/.env.example frontend/.env.local; fi
(
    cd backend
    if php -r 'exit(preg_match("/^APP_KEY=.+$/m", file_get_contents(".env")) ? 1 : 0);'; then php artisan key:generate --no-interaction; fi
    if [ ! -f database/database.sqlite ]; then touch database/database.sqlite; fi
    php artisan migrate --no-interaction
    php artisan db:seed --no-interaction
)
npm run build
printf '\nSetup complete. Create the root administrator with:\n  cd backend && php artisan app:create-admin\nThen run npm run dev from the repository root.\n'
