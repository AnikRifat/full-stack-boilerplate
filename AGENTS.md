# Generic Full Stack Starter

Reusable boilerplate for arbitrary products: Laravel API/Livewire admin and Next.js frontend. No domain modules, tenancy, payments or messaging integrations.

## Stack and layout

- `backend/`: Laravel 13, PHP 8.3+, Livewire 4, Sanctum, PHPUnit/Pint, Vite/Tailwind 4.
- `frontend/`: Next.js 16 App Router, React 19, strict TypeScript, Tailwind 4, npm.
- Database: SQLite locally and in isolated tests; native Laravel MySQL/PostgreSQL configuration is available.
- Deploy target: unspecified. Both PHP and Node are required for the full stack; shared-hosting constraints are documented.
- Read each application's own `AGENTS.md` before edits. Product/developer documentation lives in `docs/`.

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

Roles are config system roles plus database custom overlays, one primary role plus extra system roles, and personal denials. Owner access is protected. See `docs/permissions.md` before modifying access rules.

ALL permanent image/file operations go through `backend/app/Services/MediaService.php` and the `media` table. Reuse `HasMedia` for owners; never call storage directly in feature code. Storage selection is environment-driven, but old records retain their original disk. Keep secrets outside application settings.

## Checks

Run the relevant backend tests and Pint, plus frontend lint/types/build. Exercise affected UI flows before claiming completion. Do not substitute a fake S3 test for live provider verification.

## Agent setup

`CLAUDE.md` files import the `AGENTS.md` beside them, so Claude Code, Codex and other agents read one source. Edit `AGENTS.md`; never fork the content into `CLAUDE.md`.

| Runtime | Loads | Project tools |
| --- | --- | --- |
| Claude Code | `CLAUDE.md` → `@AGENTS.md`, nested ones when it works in `backend/` or `frontend/` | `.mcp.json` (Laravel Boost), `.claude/settings.json` (permissions) |
| Codex | Root `AGENTS.md`, then the nearest nested `AGENTS.md` | `.codex/config.toml` (Laravel Boost) |

A configured MCP server is not proof that it runs in the current session. Check the tool list before you rely on Boost.

### Documentation and evidence

| Need | Source |
| --- | --- |
| Laravel, Livewire, Sanctum API | Boost `search-docs` |
| Next.js API | `frontend/node_modules/next/dist/docs/` |
| Any other library, when installed | `ctx7` CLI (Context7) |

Quote a failing check's real output. When a compressor such as RTK condensed it, re-run it unfiltered (`rtk bypass npm run check`) before you quote it.

### Where knowledge goes

| Kind of knowledge | Home |
| --- | --- |
| A rule for one application | That application's `AGENTS.md` |
| A rule for the whole repository | This file |
| A backend rule tied to file paths | `backend/.ai/rules` through Boost `record-rule`, only when a person asks for a rule |
| Product behavior, API contract, decisions | `docs/` |
| A personal preference or a note for one developer | The agent's own memory, never a committed file |

Keep each `AGENTS.md` short: commands, boundaries and traps that the code does not show. Remove a line when the code makes it obvious or false.

Do not deploy, publish, or run git-writing workflows without an explicit request.
