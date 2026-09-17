# Roles and permissions

This starter follows Shoplagbe's current identity implementation, with the tenant/store dimension removed.

| Rule | Behavior |
| --- | --- |
| Protected root | `owner` always holds the complete registered catalogue while active; hidden from account management, not assignable, editable, deletable or disabled from admin screens |
| System roles | Defined in `backend/config/permissions.php`; fixed names and grants, including `*` / `module.*` expansion |
| Database overlays | `role_permissions` stores custom roles and the assignment availability of system roles; a database row cannot override a system role's grants |
| Primary role | One enabled system/custom role per account |
| Extra roles | System roles only; permissions are the union of primary and extra roles |
| Personal overrides | `users.denied_permissions` subtracts from that union; it cannot grant an ability missing from the roles |
| Disabled role | Unavailable for new assignments; existing holders retain their grants, matching Shoplagbe's current code |
| Custom deletion | Refused while any account holds the role |
| Rename | Changes a custom label, never the stored key |
| Account suspension | Denies protected admin and API requests immediately |

Default roles are `administrator`, `employee`, and `member`; the protected `owner` is separate. Members can manage their own files but cannot enter the admin. Employees can enter the basic dashboard and manage their own files; add or stack a suitable role to grant administration abilities. Administrator grants all registered abilities without making that person a protected root account.

Gates and Livewire actions enforce access on the server. The API applies the same catalogue and media policies; Sanctum token scopes add another restriction. `/me` exposes only the current user's own identity and effective permissions for UI decisions. UI visibility is never the authorization boundary.

Account creation defaults to `member`, or `employee` on the employee screen. Changing a primary role or stacked roles requires `roles.assign`; editing personal denials requires `permissions.manage`. Account create/update permissions do not implicitly grant these abilities. Treat account update permissions as trusted administration access: they include changing another non-root account's email and password.

Role resolution is a scoped service, with overlay state refreshed after writes. Add a concrete ability to `permissions.catalogue` before gating new code; add it to a system role or select it in a custom role to grant access. Custom-role editors accept catalogue abilities only, never arbitrary wildcard strings.

Shoplagbe has some old comments describing an earlier config-only version and refusal to disable assigned roles. Its current actions support database custom roles and permit assignment disabling while preserving held grants; this starter follows that implemented behavior. Multi-tenancy, legacy commerce aliases, invitation workflows, platform roles, and live permission broadcasts are domain features and are excluded.
