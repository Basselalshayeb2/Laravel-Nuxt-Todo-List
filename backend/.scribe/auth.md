# Authenticating requests

To authenticate requests, include a **`Cookie`** header with the value **`"{YOUR_SESSION_COOKIE}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

Authentication is browser-managed. Do not store a bearer token in localStorage.
