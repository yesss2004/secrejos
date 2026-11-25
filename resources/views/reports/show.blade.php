@extends('layouts.app')

@section('title', 'Detail Laporan')

@section('content')
    <x-page-header
        eyebrow="Detail Laporan"
        title="Rincian Laporan"
        subtitle="Dokumen laporan sekretariat beserta status persetujuan dan lampirannya."
        :back="route('reports.index')"
    >
        <a href="{{ route('reports.print', $report) }}" target="_blank" class="btn-secondary">
            <x-icon name="printer" class="h-4 w-4" /> Cetak
        </a>
        @if($report->anyflip_url)
            <a href="{{ $report->anyflip_url }}" target="_blank" rel="noopener noreferrer" class="btn-primary">
                <x-icon name="book" class="h-4 w-4" /> Buka Buku Digital
            </a>
        @else
            <a href="https://anyflip.com/" target="_blank" rel="noopener noreferrer" class="btn-secondary">
                <x-icon name="book" class="h-4 w-4" /> Buat Buku AnyFlip
            </a>
        @endcan
        @can('update', $report)
            <a href="{{ route('reports.edit', $report) }}" class="btn-secondary">
                <x-icon name="pencil" class="h-4 w-4" /> Edit
            </a>
        @endcan
        @can('delete', $report)
            <form method="POST" action="{{ route('reports.destroy', $report) }}" data-confirm="Hapus laporan &quot;{{ $report->title }}&quot;? Tindakan ini tidak dapat dibatalkan.">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger"><x-icon name="trash" class="h-4 w-4" /> Hapus</button>
            </form>
        @endcan
    </x-page-header>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <div class="space-y-5 xl:col-span-2">
            <div class="card overflow-hidden">
                <div class="relative border-b border-slate-100 bg-gradient-to-br from-slate-50 via-white to-emerald-50/30 px-5 py-6 sm:px-7">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="badge {{ $report->type === 'tahunan' ? 'badge-violet' : 'badge-emerald' }}">
                            <x-icon :name="$report->type === 'tahunan' ? 'chart' : 'calendar'" class="h-3 w-3" /> {{ $report->type_label }}
                        </span>
                        <span class="badge badge-slate">{{ $report->period_label }}</span>
                        <x-status-badge :tone="$report->status_tone" :label="$report->status_label" :icon="$report->status_icon" />
                    </div>

                    <h1 class="mt-4 text-[22px] font-extrabold leading-snug tracking-[-0.02em] text-ink-900 sm:text-[26px]">{{ $report->title }}</h1>

                    <div class="mt-5 flex flex-wrap items-center gap-x-5 gap-y-2.5 text-[11px] text-slate-500">
                        <span class="flex items-center gap-1.5"><x-avatar :user="$report->user" size="xs" /> {{ $report->user?->name }}</span>
                        <span class="flex items-center gap-1.5"><x-icon name="calendar" class="h-3.5 w-3.5 text-slate-400" /> Dibuat {{ $report->created_at->locale('id')->translatedFormat('d F Y, H:i') }}</span>
                        <span class="flex items-center gap-1.5"><x-icon name="paperclip" class="h-3.5 w-3.5 text-slate-400" /> {{ $report->attachments->count() }} lampiran</span>
                    </div>
                </div>

                <div class="px-5 py-6 sm:px-7">
                    <div class="mb-3 flex items-center gap-2">
                        <span class="h-1 w-5 rounded-full bg-emerald-500"></span>
                        <h2 class="text-[12px] font-extrabold uppercase tracking-[0.12em] text-slate-500">Isi Laporan</h2>
                    </div>
                    <div class="whitespace-pre-line text-[13.5px] leading-[1.85] text-slate-600">{{ $report->description }}</div>
                </div>
            </div>

            @if($report->plan)
                <div class="card flex flex-col gap-3 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                    <div>
                        <h2 class="text-[13px] font-extrabold text-ink-900">Dokumen Perencanaan Terkait</h2>
                        <p class="mt-1 text-[11px] text-slate-500">{{ $report->plan->sub_activity_code }} &middot; {{ $report->plan->sub_activity_name }}</p>
                    </div>
                    <a href="{{ route('plans.show', $report->plan) }}" class="btn-primary btn-sm">Lihat Rincian Perencanaan</a>
                </div>
            @endif

            @if($report->anyflip_url)
                <div class="card overflow-hidden">
                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                        <div class="flex items-center gap-2.5">
                            <span class="icon-tile tone-violet h-9 w-9 rounded-lg"><x-icon name="book" class="h-4 w-4" /></span>
                            <div>
                                <h2 class="text-[14px] font-extrabold text-ink-900">Buku Digital AnyFlip</h2>
                                <p class="text-[11px] text-slate-500">Pratinjau laporan dalam format halaman buku.</p>
                            </div>
                        </div>
                        <a href="{{ $report->anyflip_url }}" target="_blank" rel="noopener noreferrer" class="btn-secondary btn-sm">
                            <x-icon name="external" class="h-3.5 w-3.5" /> Buka Penuh
                        </a>
                    </div>
                    <div class="aspect-[16/10] min-h-[420px] bg-slate-100">
                        <iframe
                            src="{{ $report->anyflip_url }}"
                            title="Buku Digital {{ $report->title }}"
                            class="h-full w-full border-0"
                            allowfullscreen
                            loading="lazy"
                        ></iframe>
                    </div>
                </div>
            @endif

            @if($report->attachments->count())
                <div class="card p-5 sm:p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="icon-tile tone-sky h-9 w-9 rounded-lg"><x-icon name="paperclip" class="h-4 w-4" /></span>
                            <div>
                                <h2 class="text-[14px] font-extrabold text-ink-900">Lampiran Pendukung</h2>
                                <p class="text-[11px] text-slate-500">{{ $report->attachments->count() }} dokumen terunggah</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-semibold text-slate-400">{{ number_format($report->total_attachment_size / 1024, 0) }} KB total</span>
                    </div>

                    <div class="space-y-2">
                        @foreach($report->attachments as $attachment)
                            <div class="file-row">
                                <span class="icon-tile tone-{{ $attachment->icon_tone }} h-9 w-9 rounded-lg text-[9px] font-extrabold">{{ $attachment->extension }}</span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-xs font-semibold text-slate-700">{{ $attachment->file_name }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $attachment->human_size }} &middot; diunggah {{ $attachment->created_at->diffForHumans() }}</p>
                                </div>
                                <a href="{{ route('attachments.download', $attachment) }}" class="btn-secondary btn-sm">
                                    <x-icon name="download" class="h-3.5 w-3.5" /> Unduh
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @can('review', $report)
                <div class="card overflow-hidden">
                    <div class="flex items-center gap-2.5 border-b border-slate-100 px-5 py-4">
                        <span class="icon-tile tone-amber h-9 w-9 rounded-lg"><x-icon name="shield" class="h-4 w-4" /></span>
                        <div>
                            <h2 class="text-[14px] font-extrabold text-ink-900">Panel Verifikasi Laporan</h2>
                            <p class="text-[11px] text-slate-500">Setujui atau tolak laporan dengan catatan yang jelas.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('reports.review', $report) }}" class="p-5 sm:p-6">
                        @csrf
                        @method('PATCH')

                        <label for="review_note" class="label">Catatan review</label>
                        <textarea id="review_note" name="review_note" rows="4"
                                  placeholder="Tuliskan catatan untuk penyusun laporan. Wajib diisi bila laporan ditolak."
                                  class="textarea @error('review_note') !border-rose-400 @enderror">{{ old('review_note', $report->review_note) }}</textarea>
                        @error('review_note')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                        @error('decision')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror

                        <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-end">
                            <button type="submit" name="decision" value="rejected" class="btn-danger sm:order-1">
                                <x-icon name="x-circle" class="h-4 w-4" /> Tolak Laporan
                            </button>
                            <button type="submit" name="decision" value="approved" class="btn-primary sm:order-2">
                                <x-icon name="check-circle" class="h-4 w-4" /> Setujui Laporan
                            </button>
                        </div>
                    </form>
                </div>
            @endcan
        </div>

        <div class="space-y-5">
            <div class="card p-5">
                <h2 class="text-[13px] font-extrabold text-ink-900">Status Persetujuan</h2>

                <div class="mt-4 space-y-0">
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-100 text-emerald-600"><x-icon name="pencil" class="h-3.5 w-3.5" /></span>
                            <span class="my-1 h-7 w-px bg-slate-200"></span>
                        </div>
                        <div class="pb-3">
                            <p class="text-[12px] font-bold text-slate-800">Laporan disusun</p>
                            <p class="text-[10px] text-slate-400">{{ $report->user?->name }} &middot; {{ $report->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <span class="flex h-7 w-7 items-center justify-center rounded-full
                                {{ $report->status === 'pending' ? 'bg-amber-100 text-amber-600' : ($report->status === 'approved' ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600') }}">
                                <x-icon :name="$report->status_icon" class="h-3.5 w-3.5" />
                            </span>
                            <span class="my-1 h-7 w-px bg-slate-200"></span>
                        </div>
                        <div class="pb-3">
                            <p class="text-[12px] font-bold text-slate-800">
                                {{ $report->status === 'pending' ? 'Menunggu review' : ($report->status === 'approved' ? 'Laporan disetujui' : 'Laporan ditolak') }}
                            </p>
                            <p class="text-[10px] text-slate-400">
                                @if($report->reviewed_at)
                                    {{ $report->reviewer?->name }} &middot; {{ $report->reviewed_at->format('d M Y, H:i') }}
                                @else
                                    Belum ditinjau administrator
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <div>
                            <span class="flex h-7 w-7 items-center justify-center rounded-full {{ $report->status === 'approved' ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400' }}">
                                <x-icon name="archive" class="h-3.5 w-3.5" />
                            </span>
                        </div>
                        <div>
                            <p class="text-[12px] font-bold text-slate-800">Diarsipkan</p>
                            <p class="text-[10px] text-slate-400">{{ $report->status === 'approved' ? 'Siap dicetak dan didistribusikan' : 'Menunggu persetujuan' }}</p>
                        </div>
                    </div>
                </div>

                @if($report->review_note)
                    <div class="mt-4 rounded-xl border {{ $report->status === 'approved' ? 'border-emerald-200 bg-emerald-50/60' : 'border-rose-200 bg-rose-50/60' }} p-3.5">
                        <p class="text-[10px] font-bold uppercase tracking-wider {{ $report->status === 'approved' ? 'text-emerald-700' : 'text-rose-700' }}">Catatan reviewer</p>
                        <p class="mt-1 text-[11px] leading-relaxed {{ $report->status === 'approved' ? 'text-emerald-800' : 'text-rose-800' }}">{{ $report->review_note }}</p>
                    </div>
                @endif
            </div>

            <div class="card p-5">
                <h2 class="text-[13px] font-extrabold text-ink-900">Informasi Dokumen</h2>
                <dl class="mt-3.5 space-y-2.5 text-[11px]">
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-slate-400">Jenis</dt>
                        <dd class="font-semibold text-slate-700">{{ $report->type_label }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-slate-400">Periode</dt>
                        <dd class="font-semibold text-slate-700">{{ $report->period_label }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-slate-400">Penyusun</dt>
                        <dd class="font-semibold text-slate-700">{{ $report->user?->name }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-slate-400">Dibuat</dt>
                        <dd class="font-semibold text-slate-700">{{ $report->created_at->format('d/m/Y') }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-slate-400">Terakhir diubah</dt>
                        <dd class="font-semibold text-slate-700">{{ $report->updated_at->diffForHumans() }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-slate-400">ID Dokumen</dt>
                        <dd class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-[10px] font-semibold text-slate-600">LAP-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-2xl border border-emerald-200/60 bg-gradient-to-br from-emerald-50 to-teal-50/40 p-5">
                <div class="flex items-center gap-2">
                    <x-icon name="printer" class="h-4 w-4 text-emerald-600" />
                    <h2 class="text-[12px] font-extrabold text-emerald-900">Butuh versi cetak?</h2>
                </div>
                <p class="mt-1.5 text-[11px] leading-relaxed text-emerald-800/80">Gunakan format cetak resmi untuk keperluan arsip fisik atau distribusi pimpinan.</p>
                <a href="{{ route('reports.print', $report) }}" target="_blank" class="btn-secondary btn-sm mt-3 w-full bg-white/80">
                    <x-icon name="printer" class="h-3.5 w-3.5" /> Buka Format Cetak
                </a>
            </div>
        </div>
    </div>
@endsection
