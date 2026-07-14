<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class AuthController extends Controller
{
    /**
     * Log in
     *
     * Start a stateful Sanctum session.
     *
     * @group Authentication
     *
     * @unauthenticated
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        return ApiResponse::success(new UserResource($request->user()), 'Authenticated successfully.');
    }

    /**
     * Log out
     *
     * Invalidate the current session and CSRF token.
     *
     * @group Authentication
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Auth::forgetGuards();

        return ApiResponse::success(null, 'Logged out successfully.');
    }

    /**
     * Current user
     *
     * @group Authentication
     */
    public function user(Request $request): UserResource
    {
        return (new UserResource($request->user()))->additional(['success' => true]);
    }
}
