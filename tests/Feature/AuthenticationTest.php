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

    public function test_user_can_login_via_root_post(): void
    {
        $user = User::factory()->create([
            'email' => 'admin_root@cargotrace.test',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $response = $this->post('/', [
            'email' => 'admin_root@cargotrace.test',
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

    public function test_root_url_displays_login_page_for_guests(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Sign in to');
        $response->assertSee('CargoTrace');
        $response->assertSee('Sign In to Account');
    }

    public function test_authenticated_user_visiting_root_is_redirected_to_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'operator']);

        $response = $this->actingAs($user)->get('/');
        $response->assertRedirect('/dashboard');
    }

    public function test_unauthenticated_user_accessing_dashboard_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/');
    }

    public function test_admin_login_redirects_to_dashboard_with_role_access(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin_test@cargotrace.test',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => 'admin_test@cargotrace.test',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($admin);

        $dashResponse = $this->actingAs($admin)->get('/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Admin Operations Dashboard');
    }

    public function test_customer_login_redirects_to_dashboard_with_scoped_access(): void
    {
        $customer = User::factory()->create([
            'email' => 'customer_test@cargotrace.test',
            'password' => bcrypt('password123'),
            'role' => 'customer',
        ]);

        $response = $this->post('/login', [
            'email' => 'customer_test@cargotrace.test',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($customer);

        $dashResponse = $this->actingAs($customer)->get('/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Customer Cargo Dashboard');
    }

    public function test_user_can_logout_and_session_is_invalidated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_public_tracking_is_accessible_without_login(): void
    {
        $response = $this->get('/tracking');
        $response->assertStatus(200);
        $response->assertSee('Track Vehicles');
    }

    public function test_api_login_rejects_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'invalid_api@cargotrace.test',
            'password' => bcrypt('correct_password'),
            'role' => 'customer',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'invalid_api@cargotrace.test',
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('success', false);
    }

    public function test_api_register_user_profile_and_logout_flow(): void
    {
        $registerResponse = $this->postJson('/api/register', [
            'name' => 'EV Importer Europe',
            'email' => 'importer@euroev.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'company_name' => 'EuroEV Mobility BV',
            'phone' => '+31 10 555 0199',
        ]);

        $registerResponse->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.role', 'customer');

        $token = $registerResponse->json('data.token');

        $this->withToken($token)
            ->getJson('/api/user')
            ->assertStatus(200)
            ->assertJsonPath('data.email', 'importer@euroev.test');

        $this->withToken($token)
            ->postJson('/api/logout')
            ->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_demo_accounts_can_login_with_default_password(): void
    {
        $this->seed();

        $demoRoles = [
            'admin@cargotrace.test' => 'Admin Operations Dashboard',
            'operator@cargotrace.test' => 'Operator Dispatch Dashboard',
            'customer@cargotrace.test' => 'Customer Cargo Dashboard',
        ];

        foreach ($demoRoles as $email => $expectedText) {
            $response = $this->post('/login', [
                'email' => $email,
                'password' => 'password',
            ]);

            $response->assertRedirect('/dashboard');

            $dashResponse = $this->get('/dashboard');
            $dashResponse->assertStatus(200);
            $dashResponse->assertSee($expectedText);

            $this->post('/logout');
            $this->assertGuest();
        }
    }

    public function test_demo_login_respects_configuration_flag(): void
    {
        $this->seed();

        config(['cargotrace.demo_login_enabled' => false]);
        $this->get('/demo-login/admin')->assertStatus(403);

        config(['cargotrace.demo_login_enabled' => true]);
        $this->get('/demo-login/admin')->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }
}
