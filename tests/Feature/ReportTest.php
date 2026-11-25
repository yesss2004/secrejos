<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\TargetSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_add_an_anyflip_book_link_to_a_report(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff)->post('/reports', [
            'title' => 'Laporan Buku Digital November',
            'type' => 'bulanan',
            'year' => '2026',
            'month' => '11',
            'description' => 'Laporan November yang akan disimpan dalam bentuk buku digital AnyFlip.',
            'anyflip_url' => 'https://anyflip.com/example/abcd/',
        ])->assertRedirect();

        $this->assertDatabaseHas('reports', [
            'title' => 'Laporan Buku Digital November',
            'anyflip_url' => 'https://anyflip.com/example/abcd/',
            'user_id' => $staff->id,
        ]);
    }

    public function test_staff_can_create_a_report(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff)->post('/reports', [
            'title' => 'Laporan Kinerja November 2026',
            'type' => 'bulanan',
            'year' => '2026',
            'month' => '11',
            'description' => 'Rekap kegiatan sekretariat selama bulan November 2026 secara lengkap.',
        ])->assertRedirect();

        $this->assertDatabaseHas('reports', [
            'title' => 'Laporan Kinerja November 2026',
            'user_id' => $staff->id,
            'status' => 'pending',
        ]);
    }

    public function test_operator_can_submit_a_report_but_cannot_review_it(): void
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $report = Report::create([
            'title' => 'Laporan Operator',
            'type' => 'bulanan',
            'year' => 2026,
            'month' => 11,
            'description' => 'Laporan bulanan untuk menguji batas akses operator.',
            'status' => 'pending',
            'user_id' => $operator->id,
        ]);

        $this->actingAs($operator)
            ->get('/reports/create')
            ->assertOk();

        $this->patch("/reports/{$report->id}/review", ['decision' => 'approved'])
            ->assertForbidden();
    }

    public function test_verifier_can_review_reports_while_leadership_is_read_only(): void
    {
        $verifier = User::factory()->create(['role' => 'verifikator']);
        $leader = User::factory()->create(['role' => 'pimpinan']);
        $author = User::factory()->create(['role' => 'operator']);
        $report = Report::create([
            'title' => 'Laporan Untuk Verifikasi',
            'type' => 'bulanan',
            'year' => 2026,
            'month' => 11,
            'description' => 'Laporan yang akan diperiksa oleh verifikator dan pimpinan.',
            'status' => 'pending',
            'user_id' => $author->id,
        ]);

        $this->actingAs($leader)
            ->get('/reports')
            ->assertOk()
            ->assertSee('Laporan Untuk Verifikasi');
        $this->get("/reports/{$report->id}")
            ->assertOk()
            ->assertDontSee('Panel Verifikasi Laporan');
        $this->get('/reports/create')->assertForbidden();
        $this->patch("/reports/{$report->id}/review", ['decision' => 'approved'])->assertForbidden();

        $this->actingAs($verifier)
            ->get("/reports/{$report->id}")
            ->assertOk()
            ->assertSee('Panel Verifikasi Laporan');

        $this->patch("/reports/{$report->id}/review", [
            'decision' => 'approved',
        ])->assertRedirect("/reports/{$report->id}");

        $this->assertDatabaseHas('reports', ['id' => $report->id, 'status' => 'approved']);
    }

    public function test_reports_can_be_filtered_by_report_type(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        Report::create([
            'title' => 'Laporan Bulanan November',
            'type' => 'bulanan',
            'year' => 2026,
            'month' => 11,
            'description' => 'Rekap laporan bulanan untuk menguji filter jenis laporan.',
            'status' => 'pending',
            'user_id' => $staff->id,
        ]);
        Report::create([
            'title' => 'Laporan Tahunan 2026',
            'type' => 'tahunan',
            'year' => 2026,
            'description' => 'Rekap laporan tahunan untuk menguji filter jenis laporan.',
            'status' => 'pending',
            'user_id' => $staff->id,
        ]);

        $response = $this->actingAs($staff)
            ->get('/reports?type=tahunan')
            ->assertOk()
            ->assertSee('Semua jenis laporan')
            ->assertSee('Laporan Bulanan')
            ->assertSee('Laporan Tahunan')
            ->assertSee('Laporan Tahunan 2026');

        $this->assertSame(
            ['Laporan Tahunan 2026'],
            $response->viewData('reports')->getCollection()->pluck('title')->all()
        );
    }

    public function test_report_dashboard_shows_monthly_progress_and_print_summaries(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        Report::create([
            'title' => 'Rekap November',
            'type' => 'bulanan',
            'year' => 2026,
            'month' => 11,
            'description' => 'Rekap kegiatan pada periode November untuk pengujian dashboard laporan.',
            'status' => 'pending',
            'user_id' => $staff->id,
        ]);
        TargetSchedule::create([
            'user_id' => $staff->id,
            'title' => 'Target Monitoring',
            'due_date' => now()->addDays(3)->toDateString(),
            'priority' => 'sedang',
            'progress' => 60,
        ]);

        $this->actingAs($staff)
            ->get('/reports')
            ->assertOk()
            ->assertSee('Laporan &amp; Rekapitulasi')
            ->assertDontSee('Buku Digital AnyFlip')
            ->assertSee('Rekap Bulanan')
            ->assertSee('Realisasi / Progres')
            ->assertSee('60%')
            ->assertSee('Administrasi / Lampiran')
            ->assertSee('Laporan Siap Cetak')
            ->assertSee('Cetak laporan');
    }

    public function test_report_description_must_be_at_least_twenty_characters(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff)->from('/reports/create')->post('/reports', [
            'title' => 'Laporan Singkat',
            'type' => 'bulanan',
            'year' => '2026',
            'month' => '11',
            'description' => 'Terlalu pendek',
        ])->assertRedirect('/reports/create')->assertSessionHasErrors('description');
    }

    public function test_admin_can_approve_a_pending_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);

        $report = Report::create([
            'title' => 'Laporan Menunggu Review',
            'type' => 'bulanan',
            'year' => 2026,
            'month' => 11,
            'description' => 'Laporan yang akan diuji proses persetujuannya oleh administrator.',
            'status' => 'pending',
            'user_id' => $staff->id,
        ]);

        $this->actingAs($admin)->patch("/reports/{$report->id}/review", [
            'decision' => 'approved',
        ])->assertRedirect("/reports/{$report->id}");

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => 'approved',
            'reviewer_id' => $admin->id,
        ]);
    }

    public function test_rejection_requires_a_review_note(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);

        $report = Report::create([
            'title' => 'Laporan Perlu Perbaikan',
            'type' => 'bulanan',
            'year' => 2026,
            'month' => 11,
            'description' => 'Laporan yang akan diuji proses penolakan oleh administrator.',
            'status' => 'pending',
            'user_id' => $staff->id,
        ]);

        $this->actingAs($admin)->patch("/reports/{$report->id}/review", [
            'decision' => 'rejected',
        ])->assertSessionHasErrors('review_note');
    }

    public function test_staff_cannot_review_reports(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $author = User::factory()->create(['role' => 'staff']);

        $report = Report::create([
            'title' => 'Laporan Staf',
            'type' => 'bulanan',
            'year' => 2026,
            'month' => 11,
            'description' => 'Laporan yang tidak boleh direview oleh sesama staf sekretariat.',
            'status' => 'pending',
            'user_id' => $author->id,
        ]);

        $this->actingAs($staff)->patch("/reports/{$report->id}/review", [
            'decision' => 'approved',
        ])->assertForbidden();

        $this->assertDatabaseHas('reports', ['id' => $report->id, 'status' => 'pending']);
    }

    public function test_staff_cannot_edit_an_approved_report(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $report = Report::create([
            'title' => 'Laporan Disetujui',
            'type' => 'bulanan',
            'year' => 2026,
            'month' => 11,
            'description' => 'Laporan yang telah disetujui dan tidak boleh diubah kembali oleh staf.',
            'status' => 'approved',
            'user_id' => $staff->id,
        ]);

        $this->actingAs($staff)->get("/reports/{$report->id}/edit")->assertForbidden();
    }
}
