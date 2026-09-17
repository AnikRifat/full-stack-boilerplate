# Reference project scan

Read-only reference: `/Users/anikrfiat/Development/software/sawdahexpress-v3`.

Observed applications are `api/`, `new-admin/`, and `frontend/`. The root instructions still refer to an old `api-v3/` directory, so manifests and code were used as the source of facts.

| Area | Observed |
| --- | --- |
| Main API/admin | Laravel 13, Livewire 4, large commerce/logistics surface, JWT customer guard, legacy CMS/helpers and monitoring packages |
| New admin | Laravel 13 / Livewire 4 with native Gates, user/role/profile management, protected root setup and reusable form controls |
| Frontend | Next.js 16 / React 19 / strict TypeScript / Tailwind 4, storefront API client and client-side cookie credentials |

Kept the useful shape: one database owner, Livewire administration, separated Next.js UI, reusable accessible form controls, permission checks inside actions, and versioned JSON Resources. Initialized clean applications with the native Composer/create-next-app generators instead of copying the business code, assets, vendor directories, customer database or secrets. Replaced the source frontend's browser-readable bearer-token pattern with server actions and an HttpOnly cookie.

An existing `new-admin` PHPUnit check could not run because that reference checkout has no `vendor/bin/phpunit`. This is not a claim that the source's tests pass. No dependencies were installed and no files were changed in the source project.

Shoplagbe reference: `/Users/anikrfiat/Development/software/shoplagbe/apps/api/app/Domains/Identity`. Its current permission support/actions were inspected after the user explicitly requested matching behavior. The generic equivalent keeps protected root, config system roles, database custom roles, primary/extra roles, assignment switches, and personal denials. Store tenancy, commerce roles, legacy aliases and notification broadcasts are excluded. Its central media implementation also informed the service/trait/table boundary.
