<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LetterController extends Controller
{
    public const CLASSIFICATIONS = [
        'Undangan',
        'Nota Dinas',
        'Surat Tugas',
        'Surat Edaran',
        'Surat Keterangan',
        'Permohonan',
        'Laporan',
        'Lainnya',
    ];

    public const STATUSES = [
        'baru' => 'Baru',
        'diproses' => 'Diproses',
        'selesai' => 'Selesai',
        'arsip' => 'Diarsipkan',
    ];

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'type', 'status', 'classification']);

        $letters = Letter::with('user')
            ->filter($filters)
            ->latest('letter_date')
            ->latest('id')
            ->paginate(8)
            ->withQueryString();

        $stats = [
            'total' => Letter::count(),
            'masuk' => Letter::where('type', 'masuk')->count(),
            'keluar' => Letter::where('type', 'keluar')->count(),
            'selesai' => Letter::where('status', 'selesai')->count(),
        ];

        $classifications = Letter::query()
            ->select('classification')
            ->distinct()
            ->orderBy('classification')
            ->pluck('classification');

        $typeCounts = Letter::query()
            ->selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        return view('letters.index', compact('letters', 'stats', 'filters', 'classifications', 'typeCounts'));
    }

    public function create(Request $request): View
    {
        $suggestedType = $request->type === 'keluar' ? 'keluar' : 'masuk';

        return view('letters.create', [
            'suggestedType' => $suggestedType,
            'suggestedReference' => $this->generateReferenceNumber($suggestedType),
            'classifications' => self::CLASSIFICATIONS,
            'statuses' => self::STATUSES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reference_number' => ['required', 'string', 'max:80', 'unique:letters,reference_number'],
            'title' => ['required', 'string', 'max:160'],
            'type' => ['required', 'in:masuk,keluar'],
            'classification' => ['required', 'string', 'max:60'],
            'correspondent' => ['required', 'string', 'max:160'],
            'letter_date' => ['required', 'date', 'before_or_equal:'.now()->addDay()->toDateString()],
            'received_date' => ['nullable', 'date', 'after_or_equal:letter_date'],
            'description' => ['nullable', 'string', 'max:3000'],
            'status' => ['required', 'in:baru,diproses,selesai,arsip'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'max:10240'],
        ], $this->messages(), $this->attributes());

        DB::transaction(function () use ($validated, $request) {
            $letter = Letter::create([
                'reference_number' => strtoupper($validated['reference_number']),
                'title' => $validated['title'],
                'type' => $validated['type'],
                'classification' => $validated['classification'],
                'correspondent' => $validated['correspondent'],
                'letter_date' => $validated['letter_date'],
                'received_date' => $validated['received_date'] ?? null,
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
                'user_id' => $request->user()->id,
            ]);

            $this->storeFile($letter, $request);

            ActivityLogger::log('created', 'Mencatat '.strtolower($letter->type_label).' "'.$letter->title.'"', $letter);
        });

        return redirect()
            ->route('letters.index')
            ->with('success', 'Surat berhasil dicatat ke dalam arsip sekretariat.');
    }

    public function show(Letter $letter): View
    {
        $letter->load('user');

        return view('letters.show', [
            'letter' => $letter,
            'statuses' => self::STATUSES,
        ]);
    }

    public function edit(Letter $letter): View
    {
        return view('letters.edit', [
            'letter' => $letter,
            'classifications' => self::CLASSIFICATIONS,
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(Request $request, Letter $letter): RedirectResponse
    {
        $validated = $request->validate([
            'reference_number' => ['required', 'string', 'max:80', 'unique:letters,reference_number,'.$letter->id],
            'title' => ['required', 'string', 'max:160'],
            'type' => ['required', 'in:masuk,keluar'],
            'classification' => ['required', 'string', 'max:60'],
            'correspondent' => ['required', 'string', 'max:160'],
            'letter_date' => ['required', 'date'],
            'received_date' => ['nullable', 'date', 'after_or_equal:letter_date'],
            'description' => ['nullable', 'string', 'max:3000'],
            'status' => ['required', 'in:baru,diproses,selesai,arsip'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'max:10240'],
        ], $this->messages(), $this->attributes());

        DB::transaction(function () use ($letter, $validated, $request) {
            if ($request->hasFile('file') && $letter->file_path) {
                Storage::disk('public')->delete($letter->file_path);
            }

            $letter->update([
                'reference_number' => strtoupper($validated['reference_number']),
                'title' => $validated['title'],
                'type' => $validated['type'],
                'classification' => $validated['classification'],
                'correspondent' => $validated['correspondent'],
                'letter_date' => $validated['letter_date'],
                'received_date' => $validated['received_date'] ?? null,
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
            ]);

            $this->storeFile($letter, $request);
        });

        ActivityLogger::log('updated', 'Memperbarui arsip surat "'.$letter->title.'"', $letter);

        return redirect()
            ->route('letters.show', $letter)
            ->with('success', 'Arsip surat berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Letter $letter): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:baru,diproses,selesai,arsip'],
        ]);

        $letter->update(['status' => $validated['status']]);

        ActivityLogger::log('updated', 'Mengubah status surat "'.$letter->title.'" menjadi '.self::STATUSES[$validated['status']], $letter);

        return back()->with('success', 'Status surat berhasil diperbarui.');
    }

    public function destroy(Letter $letter): RedirectResponse
    {
        $title = $letter->title;

        DB::transaction(function () use ($letter) {
            if ($letter->file_path) {
                Storage::disk('public')->delete($letter->file_path);
            }

            $letter->delete();
        });

        ActivityLogger::log('deleted', 'Menghapus arsip surat "'.$title.'"', null);

        return redirect()
            ->route('letters.index')
            ->with('success', 'Arsip surat "'.$title.'" berhasil dihapus.');
    }

    public function download(Letter $letter): mixed
    {
        abort_unless($letter->file_path && Storage::disk('public')->exists($letter->file_path), 404, 'File surat tidak ditemukan.');

        ActivityLogger::log('downloaded', 'Mengunduh berkas surat "'.$letter->title.'"', $letter, 'download');

        $downloadName = str_replace(['/', '\\'], '-', $letter->file_name ?: basename($letter->file_path));

        return Storage::disk('public')->download($letter->file_path, $downloadName);
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = $request->only(['search', 'type', 'status', 'classification']);
        $letters = Letter::with('user')->filter($filters)->latest('letter_date')->get();

        ActivityLogger::log('exported', 'Mengekspor arsip surat ke CSV', null, 'download');

        return response()->streamDownload(function () use ($letters) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['No', 'Nomor Surat', 'Judul', 'Jenis', 'Klasifikasi', 'Pengirim/Penerima', 'Tanggal Surat', 'Status', 'Dicatat Oleh']);

            foreach ($letters as $index => $letter) {
                fputcsv($handle, [
                    $index + 1,
                    $letter->reference_number,
                    $letter->title,
                    $letter->type_label,
                    $letter->classification,
                    $letter->correspondent,
                    $letter->letter_date->format('d/m/Y'),
                    $letter->status_label,
                    $letter->user?->name,
                ]);
            }

            fclose($handle);
        }, 'arsip-surat-'.now()->format('Ymd-His').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function storeFile(Letter $letter, Request $request): void
    {
        if (! $request->hasFile('file')) {
            return;
        }

        $file = $request->file('file');
        $path = $file->store('letters/'.$letter->id, 'public');

        $letter->update([
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
        ]);
    }

    private function generateReferenceNumber(string $type): string
    {
        $prefix = $type === 'keluar' ? 'SK' : 'SM';
        $romanMonths = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $sequence = Letter::where('type', $type)->count() + 1;

        do {
            $reference = sprintf('%s/%03d/%s/%s', $prefix, $sequence, $romanMonths[now()->month - 1], now()->year);
            $exists = Letter::where('reference_number', $reference)->exists();
            $sequence++;
        } while ($exists);

        return $reference;
    }

    private function messages(): array
    {
        return [
            'reference_number.unique' => 'Nomor surat sudah digunakan. Gunakan nomor lain.',
            'letter_date.before_or_equal' => 'Tanggal surat tidak boleh melebihi hari ini.',
            'received_date.after_or_equal' => 'Tanggal diterima tidak boleh sebelum tanggal surat.',
            'file.max' => 'Ukuran berkas maksimal 10 MB.',
            'file.mimes' => 'Berkas harus berupa PDF, DOC/DOCX, XLS/XLSX, JPG, atau PNG.',
        ];
    }

    private function attributes(): array
    {
        return [
            'reference_number' => 'nomor surat',
            'title' => 'judul surat',
            'type' => 'jenis surat',
            'classification' => 'klasifikasi',
            'correspondent' => 'pengirim/penerima',
            'letter_date' => 'tanggal surat',
            'received_date' => 'tanggal diterima',
            'status' => 'status',
            'file' => 'berkas surat',
        ];
    }
}
