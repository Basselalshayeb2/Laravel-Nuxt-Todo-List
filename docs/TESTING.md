# Testing

Run all fast tests with `make test`, formatting with `make lint`, static analysis with `make analyse`, and the browser journey against a running Docker stack with `make test-e2e`.

Backend tests use an in-memory SQLite database and cover HTTP contracts, authorization, validation, query behavior, and persistence. Frontend tests use Vitest and Nuxt Test Utils for schemas, error normalization, task state, URL query rules, and permissions. Playwright uses a seeded demo user and creates a uniquely named task before editing and deleting it.

CI retains Playwright screenshots, video, traces, and Docker logs on failure. Tests must not be skipped or weakened to satisfy CI.
