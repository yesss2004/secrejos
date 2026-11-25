<?php

namespace Tests\Feature;

use App\Models\Issue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IssueTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_record_an_issue_with_cause_and_follow_up(): void
    {
        $user = User::factory()->create();
        $this->assertSame('operator', $user->role);
        $this->assertTrue($user->canManageData());

        $this->actingAs($user)->post('/kendala', [
            'title' => 'Dokumen terlambat diterima',
            'cause' => 'Dokumen pendukung belum dikirim unit terkait.',
            'follow_up' => 'Menghubungi unit terkait untuk meminta dokumen.',
            'priority' => 'tinggi',
        ])->assertRedirect('/kendala');

        $this->assertDatabaseHas('issues', [
            'user_id' => $user->id,
            'title' => 'Dokumen terlambat diterima',
            'cause' => 'Dokumen pendukung belum dikirim unit terkait.',
            'follow_up' => 'Menghubungi unit terkait untuk meminta dokumen.',
            'priority' => 'tinggi',
            'status' => 'terbuka',
        ]);
    }

    public function test_issue_list_is_scoped_to_the_signed_in_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $ownIssue = Issue::create([
            'user_id' => $user->id,
            'title' => 'Kendala saya',
            'cause' => 'Penyebab kendala saya.',
            'priority' => 'sedang',
        ]);
        Issue::create([
            'user_id' => $otherUser->id,
            'title' => 'Kendala pengguna lain',
            'cause' => 'Penyebab kendala pengguna lain.',
            'priority' => 'rendah',
        ]);

        $response = $this->actingAs($user)->get('/kendala')->assertOk();

        $this->assertSame([$ownIssue->id], $response->viewData('issues')->getCollection()->pluck('id')->all());
    }

    public function test_verifier_can_view_and_validate_issues_but_cannot_create_them(): void
    {
        $verifier = User::factory()->create(['role' => 'verifikator']);
        $operator = User::factory()->create(['role' => 'operator']);
        $issue = Issue::create([
            'user_id' => $operator->id,
            'title' => 'Kendala menunggu verifikasi',
            'cause' => 'Dokumen pendukung belum diterima.',
            'priority' => 'tinggi',
        ]);

        $this->actingAs($verifier)
            ->get('/kendala')
            ->assertOk()
            ->assertSee('Kendala menunggu verifikasi');
        $this->get('/kendala/create')->assertForbidden();

        $this->patch("/kendala/{$issue->id}/verify", ['status' => 'ditangani'])
            ->assertRedirect();

        $this->assertDatabaseHas('issues', ['id' => $issue->id, 'status' => 'ditangani']);
    }
}
