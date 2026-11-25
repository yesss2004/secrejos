<?php

namespace Tests\Feature;

use App\Models\Letter;
use App\Models\Report;
use App\Models\TargetSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_can_be_rendered_for_authenticated_user(): void
    {
        $user = User::factory()->create(['role' => 'staff']);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSeeInOrder([
                'Dashboard Utama',
                'Monitoring',
                'Kendala',
                'Target & Jadwal',
                'Laporan & Rekapitulasi',
            ]);
    }

    public function test_leadership_can_only_open_dashboard_and_reports(): void
    {
        $leader = User::factory()->create(['role' => 'pimpinan']);

        $this->actingAs($leader)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Laporan &amp; Rekapitulasi', false)
            ->assertDontSee('Target &amp; Jadwal')
            ->assertDontSee('Manajemen Surat');

        $this->get('/reports')->assertOk();
        $this->get('/letters')->assertForbidden();
        $this->get('/kendala')->assertForbidden();
        $this->get('/targets')->assertForbidden();
        $this->get('/monitoring')->assertForbidden();
    }

    public function test_dashboard_shows_monitoring_summary_from_saved_targets(): void
    {
        $user = User::factory()->create(['role' => 'staff']);
        TargetSchedule::create([
            'user_id' => $user->id,
            'title' => 'Pajak Kendaraan Dinas',
            'description' => 'Menyiapkan dokumen pembayaran.',
            'realization' => 'Dokumen sudah terkumpul.',
            'due_date' => now()->addDays(3)->toDateString(),
            'priority' => 'tinggi',
            'progress' => 35,
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Dashboard Monitoring')
            ->assertSee('Ringkasan eksekutif')
            ->assertSee('Pajak Kendaraan Dinas')
            ->assertSee('Dokumen sudah terkumpul.')
            ->assertSee('35%');
    }

    public function test_global_search_finds_reports_and_letters(): void
    {
        $user = User::factory()->create(['role' => 'staff']);

        Report::create([
            'title' => 'Laporan Evaluasi Kinerja Triwulan',
            'type' => 'bulanan',
            'year' => 2026,
            'month' => 11,
            'description' => 'Evaluasi capaian kinerja sekretariat pada triwulan berjalan.',
            'status' => 'pending',
            'user_id' => $user->id,
        ]);

        Letter::create([
            'reference_number' => 'SM/777/XI/2026',
            'title' => 'Undangan Evaluasi Kinerja',
            'type' => 'masuk',
            'classification' => 'Undangan',
            'correspondent' => 'Badan Kepegawaian',
            'letter_date' => '2026-11-10',
            'status' => 'baru',
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->get('/pencarian?q=Evaluasi')
            ->assertOk()
            ->assertSee('Laporan Evaluasi Kinerja Triwulan')
            ->assertSee('Undangan Evaluasi Kinerja');
    }

    public function test_profile_can_be_updated(): void
    {
        $user = User::factory()->create(['role' => 'staff']);

        $this->actingAs($user)->patch('/profile', [
            'name' => 'Nama Baru Staf',
            'email' => $user->email,
            'position' => 'Arsiparis Utama',
            'phone' => '081200001111',
        ])->assertRedirect('/profile');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nama Baru Staf',
            'position' => 'Arsiparis Utama',
        ]);
    }

    public function test_user_can_change_their_password(): void
    {
        $user = User::factory()->create([
            'password' => 'password',
            'role' => 'staff',
        ]);

        $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'password',
            'password' => 'password-baru-123',
            'password_confirmation' => 'password-baru-123',
        ])->assertRedirect('/profile');

        $this->assertTrue(Hash::check('password-baru-123', $user->fresh()->password));
    }

    public function test_current_password_is_required_to_change_password(): void
    {
        $user = User::factory()->create([
            'password' => 'password',
            'role' => 'staff',
        ]);

        $this->actingAs($user)->from('/profile')->put('/profile/password', [
            'current_password' => 'password-salah',
            'password' => 'password-baru-123',
            'password_confirmation' => 'password-baru-123',
        ])->assertRedirect('/profile')->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_reports_can_be_exported_as_csv(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        Report::create([
            'title' => 'Laporan Untuk Ekspor',
            'type' => 'tahunan',
            'year' => 2026,
            'description' => 'Laporan tahunan yang akan diuji proses ekspor ke berkas CSV.',
            'status' => 'approved',
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get('/reports/export');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Laporan Untuk Ekspor', $response->streamedContent());
    }
}
