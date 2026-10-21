# API contract

Base path: `/api/v1`. Send `Accept: application/json`. Authenticated requests use `Authorization: Bearer <token>`.

| Method | Endpoint | Access / result |
| --- | --- | --- |
| GET | `/health` | Public liveness: `{"data":{"status":"ok"}}` |
| GET | `/configuration` | Public allow-listed app name, support email and registration switch |
| POST | `/auth/register` | Public when registration is enabled; name/email/password/password_confirmation; 201 |
| POST | `/auth/login` | Email/password; 200 |
| POST | `/auth/logout` | Revoke current token; 204 |
| GET | `/me` | Active authenticated account, `profile:read` scope |
| PATCH | `/me` | Name only, `profile:write` scope |
| GET | `/media` | `media.view` + `media:read`; paginated visible files, 15/page |
| POST | `/media` | Multipart `file`, optional `collection`; `media.upload` + `media:write`; 201 |
| GET | `/media/{id}` | Owner or media manager with `media.view` + `media:read` |
| POST | `/media/{id}/replace` | Multipart `file`; upload/delete permission, ownership, `media:write`; new tracking record, 201 |
| DELETE | `/media/{id}` | Delete permission, ownership, `media:write`; 204 |

The Livewire admin owns user/employee/role/settings editing in this base. Those management screens are not duplicated as admin REST CRUD endpoints. Both HTTP surfaces share identity, Gates and the central media service.

Login/registration returns:

```json
{"data":{"user":{"id":1,"name":"Example","email":"example@example.test","permissions":["media.view","media.upload","media.delete"]},"token":"<returned-once>","token_type":"Bearer","expires_at":"<ISO timestamp>"}}
```

Profile responses use `{"data":{"id":1,"name":"Example","email":"example@example.test","permissions":[...]}}`. Media uses `data` Resources, with pagination `links` and `meta` on the list. Media payloads expose identity, filename, collection, MIME type, bytes, dimensions, a temporary signed URL, and creation time; they do not expose private disk paths.

Validation: 422 with `message` and field `errors`. Missing/expired token: 401. Missing permission, wrong scope, inactive account, or disabled registration: 403. Authentication throttling: 429. Laravel's other failures retain their proper HTTP status. `/up` is Laravel's built-in health route; `/health` does not prove database or S3 readiness.

The Next.js server handles credentials and calls Laravel; the browser never receives the token response. Tokens expire after 24 hours. Configure the scheduler to prune expired token rows. Logout clears the browser cookie; if remote revocation fails, the UI reports that the server-side token will expire normally.

There is no broad CORS allow-list because browser requests use Next.js server actions. For browser-direct SPA clients, configure Sanctum's stateful session authentication and explicit origins rather than storing API tokens in localStorage.

Email verification and forgotten-password flows are not included in this generic base. Add those according to the product's onboarding requirements and mail provider.
