# Tool guidance

Read the root and application `AGENTS.md` files. `config.toml` here registers Laravel Boost for a Codex session started from the repository root; `backend/.codex/config.toml` does the same for a session started in `backend/`. Codex loads project configuration only for a trusted project. A configured server is not proof that it runs in the current session.

Boost generated package guidance and skills locally. Refresh them with `php artisan boost:update` and inspect the managed changes. `backend/CLAUDE.md` stays a symlink to `AGENTS.md` because Boost writes its guidelines into the Claude file; a separate file would load them twice.

Next.js ships version-matched documentation in `frontend/node_modules/next/dist/docs/`. Preserve its managed agent rules. Product documentation belongs in `docs/`.
