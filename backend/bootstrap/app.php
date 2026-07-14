<?php

use App\Support\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

$shouldReturnJson = static fn (Request $request): bool => $request->is('api/*', 'sanctum/*') || $request->expectsJson();

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        $middleware->redirectGuestsTo(null);
    })
    ->withExceptions(function (Exceptions $exceptions) use ($shouldReturnJson): void {
        $exceptions->shouldRenderJsonWhen(fn (Request $request, Throwable $exception): bool => $shouldReturnJson($request));

        $exceptions->render(fn (ValidationException $exception, Request $request) => $shouldReturnJson($request)
            ? ApiResponse::error('The request data is invalid.', 'VALIDATION_ERROR', 422, $exception->errors()) : null);
        $exceptions->render(fn (AuthenticationException $exception, Request $request) => $shouldReturnJson($request)
            ? ApiResponse::error('Authentication is required.', 'UNAUTHENTICATED', 401) : null);
        $exceptions->render(fn (AuthorizationException $exception, Request $request) => $shouldReturnJson($request)
            ? ApiResponse::error('You are not allowed to perform this action.', 'FORBIDDEN', 403) : null);
        $exceptions->render(fn (AccessDeniedHttpException $exception, Request $request) => $shouldReturnJson($request)
            ? ApiResponse::error('You are not allowed to perform this action.', 'FORBIDDEN', 403) : null);
        $exceptions->render(fn (ModelNotFoundException $exception, Request $request) => $shouldReturnJson($request)
            ? ApiResponse::error('The requested resource was not found.', 'NOT_FOUND', 404) : null);
        $exceptions->render(fn (NotFoundHttpException $exception, Request $request) => $shouldReturnJson($request)
            ? ApiResponse::error('The requested resource was not found.', 'NOT_FOUND', 404) : null);
        $exceptions->render(fn (TokenMismatchException $exception, Request $request) => ApiResponse::error('The session or CSRF token has expired.', 'CSRF_TOKEN_MISMATCH', 419));
        $exceptions->render(fn (HttpException $exception, Request $request) => $exception->getStatusCode() === 419
            ? ApiResponse::error('The session or CSRF token has expired.', 'CSRF_TOKEN_MISMATCH', 419) : null);
        $exceptions->respond(function (Response $response, Throwable $exception, Request $request) use ($shouldReturnJson): Response {
            if ($shouldReturnJson($request) && $response->getStatusCode() >= 500) {
                return ApiResponse::error('An unexpected server error occurred.', 'SERVER_ERROR', 500);
            }

            return $response;
        });
    })->create();
