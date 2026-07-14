# Introduction

The REST API used by the TaskFlow Nuxt application.

<aside>
    <strong>Base URL</strong>: <code>http://localhost</code>
</aside>

    TaskFlow uses Laravel Sanctum's stateful cookie authentication. First request
    <code>/sanctum/csrf-cookie</code>, then submit the login endpoint with cookies enabled.
    Protected requests must keep the HTTP-only session cookie and XSRF token.

