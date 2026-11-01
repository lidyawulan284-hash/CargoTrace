<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_via_web(): void
    {
        $user = User::factory()->create([
            'email' => 'operator_test@cargotrace.test',
            'password' => bcrypt('password123'),
            'role' => 'operator',
        ]);

        $response = $this->post('/login', [
            'email' => 'operator_test@cargotrace.test',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_login_is_rejected(): void
    {
        $user = User::factory()->create([
            'email' => 'user_wrong@cargotrace.test',
            'password' => bcrypt('correct_password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'user_wrong@cargotrace.test',
            'password' => 'wrong_password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_api_login_returns_sanctum_token(): void
    {
        $user = User::factory()->create([
            'email' => 'api_user@cargotrace.test',
            'password' => bcrypt('secret123'),
            'role' => 'customer',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'api_user@cargotrace.test',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email', 'role'],
                    'token',
                    'token_type',
                ],
            ]);
    }

    public function test_unauthenticated_request_to_api_is_unauthorized(): void
    {
        $response = $this->getJson('/api/shipments');
        $response->assertStatus(401);
    }
}
