# Architecture

This is a reusable full stack starter, informed by SawdahExpress v3, without its business modules or customer data.

| Application | Owns |
| --- | --- |
| `backend/` | Laravel models, migrations, authorization, Livewire admin, and `/api/v1` JSON endpoints |
| `frontend/` | Next.js public pages, account UI, and server actions that call Laravel |

One Laravel application owns the database. Livewire and API controllers use the same identity and permission resolver. Do not make another Laravel application or let Next.js query the database directly.

## Authentication and authorization

The admin uses Laravel sessions and CSRF protection. The Next.js server acts as a backend for frontend: it calls Sanctum bearer-token endpoints and keeps the credential in an HttpOnly, SameSite cookie, with Secure enabled in production. Tokens are never returned to browser JavaScript or stored in localStorage. This avoids shared-cookie domain requirements across deployments. Browser-direct SPA authentication would instead require Sanctum's stateful cookie mode and appropriate domains/CORS configuration.

Tokens expire after 24 hours. The backend checks active account status on every protected request. Admin permission checks run in routes and Livewire actions. Roles grant only keys defined in `backend/config/permissions.php`; the active protected owner has all registered permissions. Public registration cannot assign roles, activate suspended users, or create protected owners. System roles come from config; custom role overlays and personal denials match Shoplagbe.

Database defaults to SQLite for a dependency-free local start. Laravel's standard configuration supports MySQL and PostgreSQL; test databases are isolated in-memory SQLite. Database-backed sessions, cache, and queues require the included migrations. No payment, messaging broker, tenancy, or commerce integration is included.

## Extension points

Add permission keys to the registry, Laravel models/migrations for a real module, Livewire components under `app/Livewire/Admin`, and API controllers under `app/Http/Controllers/Api/V1`. Keep HTTP validation in Form Requests and JSON representations in Resources. Add frontend server functions under `src/lib` and introduce client components only for actual interaction.

Hosting is intentionally unspecified. Laravel needs PHP 8.3+ and a writable storage directory. Next.js needs a Node runtime supporting the installed version; PHP-only shared hosting cannot run this full stack by itself. See `deployment.md` for preparation guidance.
