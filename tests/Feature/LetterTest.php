<?php

namespace Tests\Feature;

use App\Models\Letter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LetterTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_archive_a_letter(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff)->post('/letters', [
            'reference_number' => 'SM/001/XI/2026',
            'title' => 'Undangan Rapat Koordinasi',
            'type' => 'masuk',
            'classification' => 'Undangan',
            'correspondent' => 'Kementerian Dalam Negeri',
            'letter_date' => '2026-11-10',
            'received_date' => '2026-11-10',
            'description' => 'Undangan rapat koordinasi sekretariat daerah.',
            'status' => 'baru',
        ])->assertRedirect('/letters');

        $this->assertDatabaseHas('letters', [
            'reference_number' => 'SM/001/XI/2026',
            'title' => 'Undangan Rapat Koordinasi',
            'user_id' => $staff->id,
        ]);
    }

    public function test_letter_reference_number_must_be_unique(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        Letter::create([
            'reference_number' => 'SM/001/XI/2026',
            'title' => 'Surat Pertama',
            'type' => 'masuk',
            'classification' => 'Undangan',
            'correspondent' => 'Instansi A',
            'letter_date' => '2026-11-10',
            'status' => 'baru',
            'user_id' => $staff->id,
        ]);

        $this->actingAs($staff)->from('/letters/create')->post('/letters', [
            'reference_number' => 'SM/001/XI/2026',
            'title' => 'Surat Duplikat',
            'type' => 'masuk',
            'classification' => 'Undangan',
            'correspondent' => 'Instansi B',
            'letter_date' => '2026-11-11',
            'status' => 'baru',
        ])->assertRedirect('/letters/create')->assertSessionHasErrors('reference_number');
    }

    public function test_letter_status_can_be_updated(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $letter = Letter::create([
            'reference_number' => 'SK/002/XI/2026',
            'title' => 'Surat Tugas',
            'type' => 'keluar',
            'classification' => 'Surat Tugas',
            'correspondent' => 'Bagian Umum',
            'letter_date' => '2026-11-10',
            'status' => 'baru',
            'user_id' => $staff->id,
        ]);

        $this->actingAs($staff)->patch("/letters/{$letter->id}/status", [
            'status' => 'selesai',
        ])->assertRedirect();

        $this->assertDatabaseHas('letters', [
            'id' => $letter->id,
            'status' => 'selesai',
        ]);
    }

    public function test_letter_download_returns_not_found_without_file(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $letter = Letter::create([
            'reference_number' => 'SM/003/XI/2026',
            'title' => 'Surat Tanpa Berkas',
            'type' => 'masuk',
            'classification' => 'Nota Dinas',
            'correspondent' => 'Instansi C',
            'letter_date' => '2026-11-10',
            'status' => 'baru',
            'user_id' => $staff->id,
        ]);

        $this->actingAs($staff)
            ->get("/letters/{$letter->id}/download")
            ->assertNotFound();
    }

    public function test_letter_file_can_be_downloaded_with_a_safe_filename(): void
    {
        Storage::fake('public');

        $staff = User::factory()->create(['role' => 'staff']);

        Storage::disk('public')->put('letters/9/surat.pdf', 'contoh berkas');

        $letter = Letter::create([
            'reference_number' => 'SM/009/XI/2026',
            'title' => 'Surat Dengan Berkas',
            'type' => 'masuk',
            'classification' => 'Undangan',
            'correspondent' => 'Instansi E',
            'letter_date' => '2026-11-10',
            'status' => 'baru',
            'file_path' => 'letters/9/surat.pdf',
            'file_name' => 'Surat-SM/009/XI/2026.pdf',
            'file_size' => 13,
            'user_id' => $staff->id,
        ]);

        $this->actingAs($staff)
            ->get("/letters/{$letter->id}/download")
            ->assertOk()
            ->assertDownload('Surat-SM-009-XI-2026.pdf');
    }
}
