# Architecture

```text
Browser -> Nginx :80
             |-- pages ----------------> Nuxt/Nitro :3000
             |-- /api, /sanctum, /up --> Laravel PHP-FPM :9000 -> SQLite
             `-- /docs ----------------> generated Scribe files
```

Laravel and Nuxt remain independently testable applications. Nginx provides one browser origin, so Sanctum can use first-party session cookies without permissive CORS. Nuxt uses a private server-side API base URL and forwards the original cookies; browser requests use relative URLs.

SQLite stores application, session, and cache data in the assessment deployment. The one-shot `migrate` container owns schema setup and deterministic demo seeding. This architecture is intentionally limited to four containers and two named volumes.

See [ADR 0001](adr/0001-sanctum-same-origin.md) for the authentication decision.
