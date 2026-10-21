# Roles and permissions

Access control is config roles plus database overlays, without a tenant or store dimension.

| Rule | Behavior |
| --- | --- |
| Super admin | `owner` (labelled "Super admin") passes every ability and policy check through `Gate::before` while active, and nothing while suspended. There is exactly one: `app:create-admin` refuses to create a second. Hidden from account management, not assignable, editable, deletable or disabled from admin screens |
| System roles | Defined in `backend/config/permissions.php` with fixed names. Their config grants (including `*` / `module.*` expansion) are defaults: editing a system role's abilities (needs `permissions.manage`) stores them in `role_permissions` |
| Database overlays | `role_permissions` stores custom roles, plus the availability and edited abilities of system roles. A system role row with `permissions = null` keeps the config default |
| Primary role | One enabled system/custom role per account |
| Extra roles | System roles only; permissions are the union of primary and extra roles |
| Personal overrides | `users.denied_permissions` subtracts from that union; it cannot grant an ability missing from the roles |
| Disabled role | Unavailable for new assignments; existing holders retain their grants |
| Custom deletion | Refused while any account holds the role |
| Rename | Changes a custom label, never the stored key |
| Account suspension | Denies protected admin and API requests immediately |

Default roles are `administrator`, `employee`, and `member`; the protected `owner` is separate. Members can manage their own files but cannot enter the admin. Employees can enter the basic dashboard and manage their own files; add or stack a suitable role to grant administration abilities. Administrator starts with all registered abilities, but it is an ordinary role: its abilities can be narrowed on the Roles screen, and it never passes checks outside the catalogue.

Gates and Livewire actions enforce access on the server. The API applies the same catalogue and media policies; Sanctum token scopes add another restriction. `/me` exposes only the current user's own identity and effective permissions for UI decisions. UI visibility is never the authorization boundary.

Account creation defaults to `member`, or `employee` on the employee screen. Changing a primary role or stacked roles requires `roles.assign`; editing personal denials requires `permissions.manage`. Account create/update permissions do not implicitly grant these abilities. Treat account update permissions as trusted administration access: they include changing another non-root account's email and password.

Role resolution is a scoped service; saving a `RolePermission` row flushes its cache. Add a concrete ability to `permissions.catalogue` before gating new code; add it to a system role or select it in a custom role to grant access. Custom-role editors accept catalogue abilities only, never arbitrary wildcard strings.

Multi-tenancy, commerce role aliases, invitation workflows, platform roles, and live permission broadcasts are domain features and are excluded.
