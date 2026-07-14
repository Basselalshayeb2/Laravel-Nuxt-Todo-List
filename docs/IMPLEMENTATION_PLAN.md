# Implementation Plan

## 1. Repository architecture

The repository is a monorepo containing separate Laravel and Nuxt applications. Laravel owns data, validation, authentication, authorization, filtering, and serialization. Nuxt owns SSR rendering, navigation, form state, and presentation. Nginx is the single public origin: application routes go to Nuxt; `/api`, `/sanctum`, `/docs`, and `/up` go to Laravel.

## 2. Backend model and API design

`UserRole` and `TaskStatus` are backed enums. Users have a role and many tasks. Tasks contain an owner, 3–255 character title, optional description/deadline, status, and timestamps. SQLite indexes support ownership, status, due-date, and owner/status filtering. Form Requests validate every endpoint, `TaskPolicy` enforces owner/admin access, and API Resources expose data plus `can.update` and `can.delete`.

The fixed endpoints are login, logout, current user, and task list/create/show/patch/delete. Lists support `status`, `search`, `sort`, `direction`, `page`, and `per_page`; sorting is whitelisted, the default is newest-first, and the maximum page size is 100. Users see their tasks while admins see all tasks. Task ownership always comes from the authenticated user.

## 3. Sanctum cookie authentication flow

The browser first requests `/sanctum/csrf-cookie`, then posts credentials to `/api/auth/login`. Laravel rate-limits attempts, regenerates the authenticated session, and stores the session identifier in an HTTP-only cookie. The XSRF cookie supplies the CSRF header; no bearer token enters browser storage. Nuxt forwards cookies during SSR and includes credentials in browser requests. Logout invalidates the session and regenerates the CSRF token. Both 401 and 419 clear frontend auth state and redirect to login.

## 4. API response and error contract

Resources return top-level `success: true` with `data`; paginated resources keep `data`, `links`, and `meta` at the same level. Create returns 201 and delete returns a 200 confirmation. Central exception rendering returns `success`, human-readable `message`, machine-readable `code`, and an `errors` object for 401, 403, 404, 419, 422, 429, and 500 responses. Production 500 responses never expose exception details.

## 5. Frontend structure

Nuxt uses `app/pages` for landing, login, and tasks; auth/guest route middleware; default/auth layouts; task form, filters, table, empty, loading, pagination, and delete components; and typed `useApi`, `useAuth`, and `useTasks` composables. Nuxt UI supplies accessible primitives, Zod validates forms, `useState` holds SSR-safe auth state, and the task URL is the source of truth for filtering, search, sort, and pagination. Search is debounced and API field errors render beside inputs.

## 6. SSR and SEO strategy

SSR remains globally enabled. The landing page is server-rendered and indexable with description, Open Graph, and environment-derived canonical metadata. Login and task pages use `noindex, nofollow`; robots rules disallow those paths. Protected initial task data is fetched during SSR with the incoming cookies and reused during hydration.

## 7. Testing matrix

- Backend feature tests: login success/failure/rate limit/logout, unauthenticated errors, user/admin visibility, creation and validation, policy behavior, CRUD, search/filter/sort/pagination, and response contracts.
- Frontend Vitest: login/task schemas, Laravel field errors, 401/419 handling, URL query synchronization, loading/empty behavior, debounce, and permission-controlled actions.
- Playwright: login, create, filter, edit, status change, delete, logout, and protected-route redirect.
- Quality gates: Pint, Larastan, ESLint, strict Nuxt typecheck, SSR build, Scribe generation, and Docker builds.

## 8. Docker Compose architecture

The default stack contains Nginx, PHP-FPM API, Nuxt/Nitro frontend, and a one-shot migration/seeding service. A named volume stores SQLite and another stores the generated application key. The API waits for migrations, Nginx waits for API/frontend health, and only Nginx publishes a port. Multi-stage production images omit development dependencies and run unprivileged application processes.

## 9. CI pipeline

GitHub Actions runs independent backend, frontend, Docker, and E2E jobs. Backend CI validates Composer, tests, formats, analyzes, audits, and checks generated API docs. Frontend CI uses Node 24/pnpm with a frozen lockfile, then lints, typechecks, tests, and builds. Docker CI builds the production stack. E2E CI starts Compose, waits for health, runs Chromium, and uploads traces/logs on failure.

## 10. Implementation milestones

1. Domain foundation: enums, schema, models, relationships, factories, demo seed data, and model coverage.
2. API/auth foundation: Sanctum, error rendering, rate limits, session endpoints, policies, resources, CRUD/query behavior, and feature tests.
3. Nuxt auth/SEO: typed API client, SSR cookie forwarding, auth state/middleware, landing and login pages.
4. Task UX: URL-driven list, filters, responsive task table, forms/modals, permissions, and frontend tests.
5. Operations: Scribe, Docker/Nginx, Make targets, repository documentation, Playwright, and CI.

### Exact files in milestone 1

Added: `backend/app/Enums/TaskStatus.php`, `backend/app/Enums/UserRole.php`, `backend/app/Models/Task.php`, `backend/database/migrations/2026_07_14_000000_create_tasks_table.php`, `backend/database/factories/TaskFactory.php`, and domain/seed tests. Modified: the original users migration, `User`, `UserFactory`, and `DatabaseSeeder`. Removed: Laravel placeholder tests. Because this is an unreleased scaffold, the original users migration is amended rather than adding a backfill migration.

## Conservative decisions

`AGENTS.md` takes precedence over optional PDF language: admin support, pagination, tests, Scribe, Docker, and CI are required. The implementation uses Laravel 13/PHP 8.4/Nuxt 4/SQLite/Sanctum, supports PATCH but not PUT, seeds users instead of adding registration, defaults new tasks to pending, permits past deadlines, returns 403 for another user's existing task, and deliberately excludes Redis, queues, full-text search, and other unnecessary infrastructure.
