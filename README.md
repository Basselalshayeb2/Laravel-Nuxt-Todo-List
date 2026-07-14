# TaskFlow

Production-style To-Do assessment built with Laravel 13, PHP 8.4, Sanctum, Nuxt 4 SSR, TypeScript, Nuxt UI, Zod, and SQLite.

## Quick start with Docker

Requirements: Docker with Compose v2.

```bash
make up
```

Open `http://localhost`. The first run builds the images, creates the SQLite database, runs migrations, and seeds demo data. Stop with `make down`; follow output with `make logs`.

Demo-only accounts:

| Role | Email | Password |
|---|---|---|
| Admin | `admin@example.com` | `Password123!` |
| User | `user@example.com` | `Password123!` |

Never reuse these credentials outside the local assessment environment.

## Architecture

Nginx exposes one origin and routes pages to Nuxt SSR while `/api`, `/sanctum`, `/docs`, and `/up` go to Laravel. Laravel owns all validation and authorization. SQLite, the session store, and cache are persisted in a named volume. See [architecture](docs/ARCHITECTURE.md) and the [implementation plan](docs/IMPLEMENTATION_PLAN.md).

## Authentication

TaskFlow uses Sanctum stateful SPA authentication:

1. `GET /sanctum/csrf-cookie`
2. `POST /api/auth/login` with email/password and `credentials: include`
3. The browser keeps the HTTP-only session cookie and XSRF cookie.
4. Protected calls use the session; SSR forwards the incoming cookie header.
5. Logout invalidates the session and regenerates the CSRF token.

No authentication token is written to localStorage or sessionStorage. A 401 or 419 clears Nuxt auth state and redirects to `/login`.

## Non-Docker development

Requirements: PHP 8.4, Composer 2, Node.js 24, and pnpm 11.

```bash
cp backend/.env.example backend/.env
cd backend
composer install
php artisan key:generate
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate --seed
php artisan serve
```

In another terminal:

```bash
cd frontend
cp .env.example .env
pnpm install --frozen-lockfile
pnpm dev
```

Nuxt proxies `/api` and `/sanctum` to Laravel during local development.

## Commands

| Command | Purpose |
|---|---|
| `make setup` | Build images and install local dependencies |
| `make up` / `make down` | Start or stop the Docker stack |
| `make logs` | Follow container logs |
| `make test` | Run backend and frontend suites |
| `make test-backend` | Run PHPUnit feature tests |
| `make test-frontend` | Run Vitest |
| `make test-e2e` | Run Playwright against the running stack |
| `make lint` | Run Pint and ESLint checks |
| `make analyse` | Run Larastan and strict Nuxt typecheck |
| `make docs` | Regenerate Scribe HTML/OpenAPI/Postman output |
| `make fresh` | **Destructively** recreate and reseed the Docker database |

## API

| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/auth/login` | Start a session |
| POST | `/api/auth/logout` | End the session |
| GET | `/api/user` | Current user |
| GET | `/api/tasks` | Search/filter/sort/paginate tasks |
| POST | `/api/tasks` | Create a task |
| GET | `/api/tasks/{task}` | Read an authorized task |
| PATCH | `/api/tasks/{task}` | Update an authorized task |
| DELETE | `/api/tasks/{task}` | Delete an authorized task |

Task statuses are `pending`, `in_progress`, and `completed`. List parameters are `status`, `search`, `sort`, `direction`, `page`, and `per_page`. Sort values are `due_date`, `status`, `title`, `created_at`, and `updated_at`; `per_page` is capped at 100.

Generated API documentation is available at `/docs`, with OpenAPI and Postman files in `backend/public/docs`.

Success resources include top-level `success: true`. Errors have a stable shape:

```json
{
  "success": false,
  "message": "Human-readable message",
  "code": "MACHINE_READABLE_CODE",
  "errors": {}
}
```

## Environment

Backend defaults are documented in `backend/.env.example`; frontend values are in `frontend/.env.example`. Production requires secure deployment-specific values for `APP_KEY`, HTTPS cookie settings, and public/internal URLs. Docker creates a local application key in a private named volume rather than baking one into an image.

## Tests and quality

See [testing documentation](docs/TESTING.md). Backend tests use in-memory SQLite. Vitest covers frontend behavior and Playwright covers the main user journey. GitHub Actions also validates formatting, static analysis, SSR builds, generated API docs, and production Docker images.

## Tradeoffs and limitations

- Seeded accounts replace registration because registration is outside the contract.
- SQLite is appropriate for the assessment and low-concurrency deployment; a higher-write production service should use PostgreSQL.
- Search is a simple title/description substring query, not full-text infrastructure.
- Admins can act on all tasks but newly created tasks always belong to the authenticated creator.
- Email verification, password reset, notifications, queues, and realtime collaboration are intentionally out of scope.
