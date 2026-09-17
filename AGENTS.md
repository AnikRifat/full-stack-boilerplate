# Generic Full Stack Starter

Reusable boilerplate for arbitrary products: Laravel API/Livewire admin and Next.js frontend. No domain modules, tenancy, payments or messaging integrations.

## Stack and layout

- `backend/`: Laravel 13, PHP 8.3+, Livewire 4, Sanctum, PHPUnit/Pint, Vite/Tailwind 4.
- `frontend/`: Next.js 16 App Router, React 19, strict TypeScript, Tailwind 4, npm.
- Database: SQLite locally and in isolated tests; native Laravel MySQL/PostgreSQL configuration is available.
- Deploy target: unspecified. Both PHP and Node are required for the full stack; shared-hosting constraints are documented.
- Read each application's own `AGENTS.md` before edits. Product/developer documentation lives in `docs/`; tool guidance belongs in `.codex/` or managed agent files.

## Commands

Run from this repository root:

```sh
npm run setup
npm run dev
npm run check
npm run build
npm run test
```

Create the protected root interactively with `cd backend && php artisan app:create-admin`. There is no default password. Do not overwrite local environment files or regenerate an existing application key. Do not commit/push unless explicitly asked.

## Boundaries

Laravel owns all data, migrations, validation and authorization. Next.js uses a server-only API helper, runtime schema validation and server actions; never expose bearer credentials in a client bundle or localStorage. Keep API contracts synchronized across apps.

Role behavior matches Shoplagbe's current code, with store tenancy removed: config system roles, database custom overlays, one primary role plus extra system roles, and personal denials. Owner access is protected. See `docs/permissions.md` before modifying access rules.

ALL permanent image/file operations go through `backend/app/Services/MediaService.php` and the `media` table. Reuse `HasMedia` for owners; never call storage directly in feature code. Storage selection is environment-driven, but old records retain their original disk. Keep secrets outside application settings.

## Checks

Run the relevant backend tests and Pint, plus frontend lint/types/build. Exercise affected UI flows before claiming completion. Do not substitute a fake S3 test for live provider verification. The backend currently has 38 passing behavior tests (recorded during initialization; update when coverage changes).

## gstack

Available global skills: office-hours, spec, review, browse, qa, qa-only, design-review, ship, land-and-deploy, canary, document-generate, document-release, retro, careful, freeze, guard and learn. Anik's project-init, arch-design, code-implement, behavior-verify and related method skills own overlapping engineering stages.

Use `/browse` from gstack for all web browsing; never use `mcp__claude-in-chrome__*`. Do not deploy or perform git-writing ship workflows without an explicit request.
