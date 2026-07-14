<?php

namespace Tests\Feature;

use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Route;
use RuntimeException;
use Tests\TestCase;

class ErrorContractTest extends TestCase
{
    public function test_csrf_expiry_uses_the_419_contract(): void
    {
        Route::get('/api/test-csrf-expiry', fn () => throw new TokenMismatchException);

        $this->getJson('/api/test-csrf-expiry')->assertStatus(419)->assertExactJson([
            'success' => false,
            'message' => 'The session or CSRF token has expired.',
            'code' => 'CSRF_TOKEN_MISMATCH',
            'errors' => [],
        ]);
    }

    public function test_unexpected_errors_are_generic(): void
    {
        Route::get('/api/test-server-error', fn () => throw new RuntimeException('Sensitive internal detail'));

        $this->getJson('/api/test-server-error')->assertInternalServerError()->assertExactJson([
            'success' => false,
            'message' => 'An unexpected server error occurred.',
            'code' => 'SERVER_ERROR',
            'errors' => [],
        ]);
    }
}
