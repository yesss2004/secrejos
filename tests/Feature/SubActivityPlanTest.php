<?php

namespace Tests\Feature;

use App\Models\SubActivityPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubActivityPlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_create_and_print_a_sub_activity_plan_with_calculated_budget_totals(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff)
            ->get('/perencanaan/create')
            ->assertOk()
            ->assertSee('Identitas Subkegiatan');

        $this->post('/perencanaan', $this->validPlanData())->assertRedirect();

        $plan = SubActivityPlan::with(['indicators', 'budgetLines'])->firstOrFail();
        $report = $plan->report;
        $this->assertNotNull($report);
        $this->assertSame($staff->id, $plan->user_id);
        $this->assertSame(1955000.0, $plan->total_before);
        $this->assertSame(1955000.0, $plan->total_after);
        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'sub_activity_plan_id' => $plan->id,
            'status' => 'pending',
        ]);

        $this->get('/reports')
            ->assertOk()
            ->assertSee($report->title)
            ->assertSee('Rekap Anggaran Subkegiatan')
            ->assertSee('1.955.000,00');

        $this->get("/reports/{$report->id}")
            ->assertOk()
            ->assertSee('Dokumen Perencanaan Terkait');
        $this->get("/reports/{$report->id}/edit")->assertForbidden();
        $this->delete("/reports/{$report->id}")->assertForbidden();

        $this->get("/perencanaan/{$plan->id}/print")
            ->assertOk()
            ->assertSee('Rincian Anggaran Belanja Subkegiatan')
            ->assertSee('1.955.000,00');

        $this->put("/perencanaan/{$plan->id}", $this->validPlanData('24000'))->assertRedirect();
        $this->assertDatabaseCount('reports', 1);
        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => 'pending',
        ]);

        $plan->refresh()->load('budgetLines');
        $report->refresh();
        $this->assertSame(2040000.0, $plan->total_after);
        $this->assertStringContainsString('Rp2.040.000,00', $report->description);
    }

    public function test_users_cannot_edit_another_users_plan(): void
    {
        $owner = User::factory()->create(['role' => 'staff']);
        $otherStaff = User::factory()->create(['role' => 'staff']);
        $plan = SubActivityPlan::create([
            'year' => 2026,
            'organization_unit' => 'Unit',
            'program_name' => 'Program',
            'activity_name' => 'Kegiatan',
            'sub_activity_code' => '1.01.01',
            'sub_activity_name' => 'Subkegiatan',
            'user_id' => $owner->id,
        ]);

        $this->actingAs($otherStaff)->get("/perencanaan/{$plan->id}/edit")->assertForbidden();
    }

    private function validPlanData(string $unitPrice = '23000'): array
    {
        return [
            'plan' => [
                'year' => '2026',
                'organization_unit' => 'Badan Penanggulangan Bencana Daerah',
                'program_name' => 'Program Penunjang Urusan Pemerintahan Daerah',
                'activity_name' => 'Administrasi Kepegawaian Perangkat Daerah',
                'sub_activity_code' => '1.05.01.2.05.0005',
                'sub_activity_name' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai',
                'funding_source' => 'Pendapatan Asli Daerah',
                'location' => 'Semua Kota/Kabupaten',
                'implementation_time' => 'Januari s.d. Desember',
            ],
            'indicators' => [[
                'category' => 'Keluaran',
                'indicator_before' => 'Jumlah dokumen',
                'benchmark_before' => 'Dokumen monitoring',
                'target_before' => '2 Dokumen',
                'indicator_after' => 'Jumlah dokumen',
                'benchmark_after' => 'Dokumen monitoring',
                'target_after' => '2 Dokumen',
            ]],
            'budget_lines' => [[
                'account_code' => '5.1.02.01.001.00052',
                'description' => 'Jamuan Makan Rapat',
                'coefficient_before' => '85',
                'unit_before' => 'Orang/Hari',
                'unit_price_before' => $unitPrice,
                'tax_before' => '0',
                'coefficient_after' => '85',
                'unit_after' => 'Orang/Hari',
                'unit_price_after' => $unitPrice,
                'tax_after' => '0',
            ]],
        ];
    }
}
