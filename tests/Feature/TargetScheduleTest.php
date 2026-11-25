<?php

namespace Tests\Feature;

use App\Models\TargetSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TargetScheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_target_with_due_date_and_priority(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/targets', [
            'title' => 'Pajak Kendaraan Dinas',
            'description' => 'Siapkan pembayaran dan bukti pajak kendaraan.',
            'realization' => 'Mengumpulkan dokumen kendaraan.',
            'due_date' => now()->addDays(5)->toDateString(),
            'priority' => 'tinggi',
            'progress' => 40,
        ])->assertRedirect('/targets');

        $this->assertDatabaseHas('target_schedules', [
            'user_id' => $user->id,
            'title' => 'Pajak Kendaraan Dinas',
            'priority' => 'tinggi',
            'realization' => 'Mengumpulkan dokumen kendaraan.',
            'progress' => 40,
            'status' => 'belum_selesai',
        ]);
    }

    public function test_monitoring_dashboard_shows_target_details_realization_and_progress(): void
    {
        $user = User::factory()->create();
        TargetSchedule::create([
            'user_id' => $user->id,
            'title' => 'Pajak Kendaraan Dinas',
            'description' => 'Menyelesaikan pembayaran pajak kendaraan.',
            'realization' => 'Dokumen kendaraan sudah dikumpulkan.',
            'due_date' => now()->addDays(5)->toDateString(),
            'priority' => 'tinggi',
            'progress' => 40,
        ]);

        $this->actingAs($user)
            ->get('/monitoring')
            ->assertOk()
            ->assertSee('Detail target')
            ->assertSee('Menyelesaikan pembayaran pajak kendaraan.')
            ->assertSee('Dokumen kendaraan sudah dikumpulkan.')
            ->assertSee('40%');
    }

    public function test_target_list_only_shows_targets_for_the_signed_in_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $ownTarget = TargetSchedule::create([
            'user_id' => $user->id,
            'title' => 'Target Saya',
            'due_date' => now()->addDays(2)->toDateString(),
            'priority' => 'sedang',
        ]);
        TargetSchedule::create([
            'user_id' => $otherUser->id,
            'title' => 'Target Pengguna Lain',
            'due_date' => now()->addDays(3)->toDateString(),
            'priority' => 'rendah',
        ]);

        $response = $this->actingAs($user)->get('/targets')->assertOk();

        $this->assertSame([$ownTarget->id], $response->viewData('targets')->getCollection()->pluck('id')->all());
    }

    public function test_user_can_mark_their_target_as_completed(): void
    {
        $user = User::factory()->create();
        $target = TargetSchedule::create([
            'user_id' => $user->id,
            'title' => 'Selesaikan rekap',
            'due_date' => now()->addDays(1)->toDateString(),
            'priority' => 'sedang',
        ]);

        $this->actingAs($user)
            ->patch("/targets/{$target->id}/status", ['status' => 'selesai'])
            ->assertRedirect();

        $this->assertDatabaseHas('target_schedules', [
            'id' => $target->id,
            'status' => 'selesai',
        ]);
    }

    public function test_user_cannot_edit_another_users_target(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $target = TargetSchedule::create([
            'user_id' => $otherUser->id,
            'title' => 'Target Pribadi',
            'due_date' => now()->addDays(1)->toDateString(),
            'priority' => 'sedang',
        ]);

        $this->actingAs($user)->get("/targets/{$target->id}/edit")->assertNotFound();
    }
}
