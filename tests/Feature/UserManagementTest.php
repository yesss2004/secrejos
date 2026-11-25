<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_user_management(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/users')->assertOk()->assertSee('Manajemen Pengguna');
        $this->get('/users/create')
            ->assertOk()
            ->assertSee('Admin/Operator Subbagian')
            ->assertSee('Verifikator/PPK')
            ->assertSee('Pimpinan/Sekretaris');
    }

    public function test_staff_cannot_access_user_management(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff)->get('/users')->assertForbidden();
    }

    public function test_admin_can_create_a_new_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post('/users', [
            'name' => 'Sekretaris Baru',
            'email' => 'sekretaris.baru@example.com',
            'position' => 'Staf Administrasi',
            'phone' => '081234567890',
            'role' => 'staff',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_active' => '1',
        ])->assertRedirect('/users');

        $this->assertDatabaseHas('users', [
            'email' => 'sekretaris.baru@example.com',
            'role' => 'staff',
            'is_active' => true,
        ]);
    }

    public function test_admin_cannot_deactivate_their_own_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAs($admin)
            ->patch("/users/{$admin->id}/toggle")
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $admin->id, 'is_active' => true]);
    }

    public function test_user_with_records_cannot_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);

        $staff->letters()->create([
            'reference_number' => 'SM/009/XI/2026',
            'title' => 'Surat Milik Staf',
            'type' => 'masuk',
            'classification' => 'Undangan',
            'correspondent' => 'Instansi D',
            'letter_date' => '2026-11-10',
            'status' => 'baru',
        ]);

        $this->actingAs($admin)
            ->delete("/users/{$staff->id}")
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $staff->id]);
    }
}
