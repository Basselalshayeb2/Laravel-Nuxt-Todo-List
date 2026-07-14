<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_fetch_profile_and_logout(): void
    {
        $user = User::factory()->create(['password' => Hash::make('Password123!')]);

        $this->postJson('/api/auth/login', ['email' => $user->email, 'password' => 'Password123!'])
            ->assertOk()->assertJsonPath('success', true)->assertJsonPath('data.email', $user->email);

        $this->getJson('/api/user')->assertOk()->assertJsonPath('data.id', $user->id);
        $this->postJson('/api/auth/logout')->assertOk()->assertJsonPath('success', true);
        $this->getJson('/api/user')->assertUnauthorized()->assertJsonPath('code', 'UNAUTHENTICATED');
    }

    public function test_login_validation_and_bad_credentials_use_error_contract(): void
    {
        User::factory()->create(['email' => 'person@example.com']);

        $this->postJson('/api/auth/login', [])->assertUnprocessable()
            ->assertJsonStructure(['success', 'message', 'code', 'errors' => ['email', 'password']]);
        $this->postJson('/api/auth/login', ['email' => 'person@example.com', 'password' => 'wrong'])
            ->assertUnprocessable()->assertJsonPath('success', false)->assertJsonPath('code', 'VALIDATION_ERROR');
    }

    public function test_login_is_rate_limited_after_repeated_failures(): void
    {
        User::factory()->create(['email' => 'limited@example.com']);

        foreach (range(1, 5) as $attempt) {
            $this->postJson('/api/auth/login', ['email' => 'limited@example.com', 'password' => 'wrong'])
                ->assertUnprocessable();
        }

        $this->postJson('/api/auth/login', ['email' => 'limited@example.com', 'password' => 'wrong'])
            ->assertStatus(429)->assertJsonPath('code', 'TOO_MANY_ATTEMPTS');
    }
}
