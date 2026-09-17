<!-- BEGIN:nextjs-agent-rules -->

# This is NOT the Next.js you know

This version has breaking changes — APIs, conventions, and file structure may all differ from your training data. Read the relevant guide in `node_modules/next/dist/docs/` (resolved from this file's directory; in monorepos the `next` package may not be visible from the repo root) before writing any code. Heed deprecation notices.

This block is written and re-added by `next dev` — verify at `node_modules/next/dist/server/lib/generate-agent-files.js`. Removing it from a diff only re-creates the uncommitted change; committing it with your work keeps the tree clean.

<!-- END:nextjs-agent-rules -->

# Frontend project rules

Read `../AGENTS.md`. Use npm, strict TypeScript, functional components, and App Router. Consult version-matched installed Next.js docs before changing APIs. Preserve the managed guidance above.

`src/lib/api.ts` and `session.ts` are server-only. `API_BASE_URL` is server configuration; tokens stay in an HttpOnly cookie. All mutations use server actions; server-side validation and Laravel authorization remain required even when UI controls are hidden. Zod validates responses and form boundaries. Request-time public settings supply app branding and registration availability. Keep framework control-flow errors out of broad error catches.

Commands: `npm run lint`, `npm run typecheck`, `npm run build`, `npm run dev`. Keep responsive layouts in a single tree, CSS tokens in `src/app/globals.css`, and shared fields/forms in `src/components/forms.tsx`. No domain-specific content or client-side bearer-token storage.

## gstack

Use `/browse` from gstack for all web browsing; never use `mcp__claude-in-chrome__*`. Available global skills and engineering routing are listed in `../AGENTS.md`.
