<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthMiddlewareTest extends TestCase
{
    private function authenticatedSession(): array
    {
        return [
            'supabase' => [
                'access_token' => 'mock_access_token_jwt',
                'refresh_token' => 'mock_refresh_token',
                'expires_at' => now()->addHour()->timestamp,
                'user' => [
                    'id' => '00000000-0000-0000-0000-000000000001',
                    'email' => 'admin@pawku.com',
                ],
                'profile' => [
                    'full_name' => 'Admin PawKu Test',
                    'role' => 'admin',
                ],
            ],
        ];
    }

    public function test_unauthenticated_user_is_redirected_from_dashboard_to_login(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
    }

    public function test_unauthenticated_user_can_view_login_and_register_pages(): void
    {
        $this->get(route('login'))->assertStatus(200);
        $this->get(route('register'))->assertStatus(200);
    }

    public function test_authenticated_user_cannot_access_login_page_and_is_redirected_to_dashboard(): void
    {
        $response = $this->withSession($this->authenticatedSession())
            ->get(route('login'));

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_authenticated_user_cannot_access_register_page_and_is_redirected_to_dashboard(): void
    {
        $response = $this->withSession($this->authenticatedSession())
            ->get(route('register'));

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $response = $this->withSession($this->authenticatedSession())
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('admin@pawku.com');
        $response->assertSee('Admin PawKu Test');
        $response->assertSee('Dashboard Administrasi');
    }

    public function test_logout_clears_session_and_redirects_to_login(): void
    {
        $response = $this->withSession($this->authenticatedSession())
            ->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertNull(session('supabase.access_token'));
    }
}
