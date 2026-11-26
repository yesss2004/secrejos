<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_can_be_rendered(): void
    {
        $this->get('/login')->assertOk()->assertSee('Masuk')->assertDontSee('Akun admin');
    }

    public function test_login_assets_and_form_use_forwarded_https_scheme(): void
    {
        $this->withHeaders(['X-Forwarded-Proto' => 'https'])
            ->get('/login')
            ->assertOk()
            ->assertSee('action="https://localhost:8000/login"', false)
            ->assertSee('https://localhost:8000/build/assets/app-', false);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'staf@example.com',
            'password' => 'password',
            'role' => 'operator',
            'is_active' => true,
        ]);

        $this->post('/login', [
            'email' => 'staf@example.com',
            'password' => 'password',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_verifier_and_leadership_accounts_can_login(): void
    {
        foreach (['verifikator', 'pimpinan'] as $role) {
            $user = User::factory()->create([
                'role' => $role,
                'is_active' => true,
            ]);

            $this->post('/login', [
                'email' => $user->email,
                'password' => 'password',
            ])->assertRedirect('/dashboard');

            $this->assertAuthenticatedAs($user);
            $this->post('/logout')->assertRedirect('/login');
        }
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'staf@example.com',
            'password' => 'password',
        ]);

        $this->from('/login')->post('/login', [
            'email' => 'staf@example.com',
            'password' => 'salah-password',
        ])->assertRedirect('/login')->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->create([
            'email' => 'nonaktif@example.com',
            'password' => 'password',
            'is_active' => false,
        ]);

        $this->post('/login', [
            'email' => 'nonaktif@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_demo_login_is_not_available(): void
    {
        User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->post('/demo-login/admin')->assertNotFound();
        $this->assertGuest();
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }
}
