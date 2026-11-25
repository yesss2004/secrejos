@extends('layouts.app')

@section('title', 'Detail Surat')

@section('content')
    <x-page-header
        eyebrow="Detail Arsip Surat"
        title="Rincian Surat"
        subtitle="Informasi lengkap surat, berkas digital, dan status tindak lanjut."
        :back="route('letters.index')"
    >
        @if($letter->file_path)
            <a href="{{ route('letters.download', $letter) }}" class="btn-secondary">
                <x-icon name="download" class="h-4 w-4" /> Unduh Berkas
            </a>
        @endif
        <a href="{{ route('letters.edit', $letter) }}" class="btn-secondary">
            <x-icon name="pencil" class="h-4 w-4" /> Edit
        </a>
        <form method="POST" action="{{ route('letters.destroy', $letter) }}" data-confirm="Hapus arsip surat &quot;{{ $letter->title }}&quot;? Tindakan ini tidak dapat dibatalkan.">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger"><x-icon name="trash" class="h-4 w-4" /> Hapus</button>
        </form>
    </x-page-header>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <div class="space-y-5 xl:col-span-2">
            <div class="card overflow-hidden">
                <div class="border-b border-slate-100 bg-gradient-to-br from-slate-50 via-white to-sky-50/30 px-5 py-6 sm:px-7">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="badge {{ $letter->type === 'masuk' ? 'badge-sky' : 'badge-violet' }}">
                            <x-icon :name="$letter->type === 'masuk' ? 'inbox' : 'send'" class="h-3 w-3" /> {{ $letter->type_label }}
                        </span>
                        <span class="badge badge-slate">{{ $letter->classification }}</span>
                        <x-status-badge :tone="$letter->status_tone" :label="$letter->status_label" />
                    </div>

                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <span class="font-mono text-[12px] font-bold tracking-wide text-slate-500">{{ $letter->reference_number }}</span>
                        <button type="button" data-copy="{{ $letter->reference_number }}" class="rounded-md bg-slate-100 px-2 py-1 text-[10px] font-bold text-slate-500 transition hover:bg-emerald-100 hover:text-emerald-700">Salin nomor</button>
                    </div>

                    <h1 class="mt-2 text-[22px] font-extrabold leading-snug tracking-[-0.02em] text-ink-900 sm:text-[26px]">{{ $letter->title }}</h1>

                    <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="flex items-center gap-3 rounded-xl border border-slate-200/70 bg-white/70 p-3">
                            <span class="icon-tile tone-slate h-9 w-9 rounded-lg"><x-icon name="building" class="h-4 w-4" /></span>
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $letter->type === 'masuk' ? 'Pengirim' : 'Penerima' }}</p>
                                <p class="truncate text-[12px] font-bold text-slate-700">{{ $letter->correspondent }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 rounded-xl border border-slate-200/70 bg-white/70 p-3">
                            <span class="icon-tile tone-slate h-9 w-9 rounded-lg"><x-icon name="calendar" class="h-4 w-4" /></span>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tanggal Surat</p>
                                <p class="text-[12px] font-bold text-slate-700">{{ $letter->letter_date->locale('id')->translatedFormat('d F Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-5 py-6 sm:px-7">
                    <div class="mb-3 flex items-center gap-2">
                        <span class="h-1 w-5 rounded-full bg-sky-500"></span>
                        <h2 class="text-[12px] font-extrabold uppercase tracking-[0.12em] text-slate-500">Ringkasan Surat</h2>
                    </div>
                    @if($letter->description)
                        <div class="whitespace-pre-line text-[13.5px] leading-[1.85] text-slate-600">{{ $letter->description }}</div>
                    @else
                        <p class="text-[13px] italic text-slate-400">Belum ada ringkasan untuk surat ini.</p>
                    @endif
                </div>
            </div>

            <div class="card p-5 sm:p-6">
                <h2 class="text-[13px] font-extrabold text-ink-900">Metadata Arsip</h2>
                <dl class="mt-4 grid grid-cols-1 gap-x-8 gap-y-3 text-[11px] sm:grid-cols-2">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                        <dt class="text-slate-400">Nomor surat</dt>
                        <dd class="font-mono font-semibold text-slate-700">{{ $letter->reference_number }}</dd>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                        <dt class="text-slate-400">Jenis</dt>
                        <dd class="font-semibold text-slate-700">{{ $letter->type_label }}</dd>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                        <dt class="text-slate-400">Klasifikasi</dt>
                        <dd class="font-semibold text-slate-700">{{ $letter->classification }}</dd>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                        <dt class="text-slate-400">Tanggal diterima</dt>
                        <dd class="font-semibold text-slate-700">{{ $letter->received_date?->locale('id')->translatedFormat('d F Y') ?? '-' }}</dd>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                        <dt class="text-slate-400">Dicatat oleh</dt>
                        <dd class="font-semibold text-slate-700">{{ $letter->user?->name }}</dd>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                        <dt class="text-slate-400">Dibuat pada</dt>
                        <dd class="font-semibold text-slate-700">{{ $letter->created_at->locale('id')->translatedFormat('d F Y, H:i') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="space-y-5">
            <div class="card p-5">
                <h2 class="text-[13px] font-extrabold text-ink-900">Perbarui Status</h2>
                <p class="mt-0.5 text-[11px] text-slate-500">Sesuaikan status tindak lanjut surat.</p>

                <form method="POST" action="{{ route('letters.status', $letter) }}" class="mt-3">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="select !py-2.5 !text-[13px]">
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" @selected($letter->status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')<p class="field-error">{{ $message }}</p>@enderror
                    <button type="submit" class="btn-primary btn-sm mt-2 w-full"><x-icon name="refresh" class="h-3.5 w-3.5" /> Simpan Status</button>
                </form>

                <div class="mt-5 space-y-0">
                    @foreach([
                        ['label' => 'Surat dicatat', 'done' => true, 'icon' => 'pencil'],
                        ['label' => 'Diproses', 'done' => in_array($letter->status, ['diproses', 'selesai', 'arsip']), 'icon' => 'refresh'],
                        ['label' => 'Selesai', 'done' => in_array($letter->status, ['selesai', 'arsip']), 'icon' => 'check'],
                        ['label' => 'Diarsipkan', 'done' => $letter->status === 'arsip', 'icon' => 'archive'],
                    ] as $step)
                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full {{ $step['done'] ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400' }}">
                                    <x-icon :name="$step['icon']" class="h-3 w-3" />
                                </span>
                                @if(! $loop->last)<span class="my-0.5 h-5 w-px bg-slate-200"></span>@endif
                            </div>
                            <p class="pt-1 text-[11px] font-semibold {{ $step['done'] ? 'text-slate-700' : 'text-slate-400' }}">{{ $step['label'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card p-5">
                <h2 class="text-[13px] font-extrabold text-ink-900">Berkas Digital</h2>
                @if($letter->file_path)
                    <div class="mt-3 flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50/60 p-3">
                        <span class="icon-tile tone-rose h-10 w-10 rounded-lg text-[9px] font-extrabold">{{ strtoupper(pathinfo($letter->file_name, PATHINFO_EXTENSION) ?: 'FILE') }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-[11px] font-bold text-slate-700">{{ $letter->file_name }}</p>
                            <p class="text-[10px] text-slate-400">{{ $letter->human_size }}</p>
                        </div>
                    </div>
                    <a href="{{ route('letters.download', $letter) }}" class="btn-secondary btn-sm mt-3 w-full"><x-icon name="download" class="h-3.5 w-3.5" /> Unduh Berkas</a>
                @else
                    <div class="mt-3 rounded-xl border border-dashed border-slate-200 p-5 text-center">
                        <x-icon name="paperclip" class="mx-auto h-5 w-5 text-slate-300" />
                        <p class="mt-2 text-[11px] font-semibold text-slate-500">Belum ada berkas</p>
                        <a href="{{ route('letters.edit', $letter) }}" class="mt-2 inline-block text-[10px] font-bold text-emerald-700 hover:text-emerald-800">Unggah berkas &rarr;</a>
                    </div>
                @endif
            </div>

            <div class="rounded-2xl border border-sky-200/60 bg-gradient-to-br from-sky-50 to-indigo-50/30 p-5">
                <div class="flex items-center gap-2">
                    <x-icon name="shield" class="h-4 w-4 text-sky-600" />
                    <h2 class="text-[12px] font-extrabold text-sky-900">Arsip terverifikasi</h2>
                </div>
                <p class="mt-1.5 text-[11px] leading-relaxed text-sky-800/80">Setiap perubahan pada arsip surat tercatat pada riwayat aktivitas untuk kebutuhan audit internal.</p>
                <a href="{{ route('activities.index') }}" class="mt-3 inline-flex items-center gap-1 text-[10px] font-bold text-sky-700 hover:text-sky-800">Lihat riwayat <x-icon name="arrow-right" class="h-3 w-3" /></a>
            </div>
        </div>
    </div>
@endsection
