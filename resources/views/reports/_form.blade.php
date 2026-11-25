@php
    $editing = isset($report) && $report;
    $selectedType = old('type', $editing ? $report->type : ($suggestedType ?? 'bulanan'));
    $selectedYear = old('year', $editing ? $report->year : now()->year);
    $selectedMonth = old('month', $editing ? $report->month : now()->month);
    $yearOptions = collect($years ?? [])->push($selectedYear)->filter()->unique()->sortDesc()->values();
    $monthNames = collect(range(1, 12))->mapWithKeys(fn ($m) => [$m => \Illuminate\Support\Carbon::createFromDate(2000, $m, 1)->locale('id')->translatedFormat('F')]);
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="grid grid-cols-1 gap-5 xl:grid-cols-3">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="space-y-5 xl:col-span-2">
        <div class="card p-5 sm:p-6">
            <div class="mb-5 flex items-start gap-3">
                <span class="icon-tile tone-emerald h-9 w-9 rounded-lg"><x-icon name="document" class="h-4 w-4" /></span>
                <div>
                    <h2 class="text-[15px] font-extrabold text-ink-900">Informasi Laporan</h2>
                    <p class="mt-0.5 text-[11px] text-slate-500">Isi data pokok laporan yang akan diarsipkan.</p>
                </div>
            </div>

            <div class="space-y-5">
                <div>
                    <label for="title" class="label">Judul laporan <span class="text-rose-500">*</span></label>
                    <input id="title" name="title" type="text" value="{{ old('title', $editing ? $report->title : '') }}" required maxlength="160"
                           placeholder="Contoh: Laporan Kinerja Sekretariat November 2026"
                           class="input @error('title') !border-rose-400 !ring-rose-500/10 @enderror">
                    @error('title')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                </div>

                <div>
                    <span class="label">Jenis laporan <span class="text-rose-500">*</span></span>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        @foreach([
                            ['value' => 'bulanan', 'label' => 'Laporan Bulanan', 'desc' => 'Rekap kinerja per bulan', 'icon' => 'calendar'],
                            ['value' => 'tahunan', 'label' => 'Laporan Tahunan', 'desc' => 'Rekap kinerja per tahun', 'icon' => 'chart'],
                        ] as $option)
                            <label class="relative cursor-pointer">
                                <input type="radio" name="type" value="{{ $option['value'] }}" data-report-type class="peer sr-only" @checked($selectedType === $option['value'])>
                                <div class="radio-card flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-3.5 transition hover:border-slate-300 peer-checked:border-emerald-500 peer-checked:bg-emerald-50/50 peer-checked:ring-1 peer-checked:ring-emerald-500">
                                    <span class="icon-tile h-9 w-9 rounded-lg {{ $option['value'] === 'tahunan' ? 'tone-violet' : 'tone-emerald' }}">
                                        <x-icon :name="$option['icon']" class="h-4 w-4" />
                                    </span>
                                    <div class="flex-1">
                                        <p class="text-[12px] font-bold text-slate-800">{{ $option['label'] }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $option['desc'] }}</p>
                                    </div>
                                    <span class="radio-dot">
                                        <span class="radio-dot-inner"></span>
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('type')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="year" class="label">Tahun periode <span class="text-rose-500">*</span></label>
                        <select id="year" name="year" required class="select @error('year') !border-rose-400 @enderror">
                            @foreach($yearOptions as $year)
                                <option value="{{ $year }}" @selected((string) $selectedYear === (string) $year)>{{ $year }}</option>
                            @endforeach
                        </select>
                        @error('year')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                    </div>

                    <div data-month-field>
                        <label for="month" class="label">Bulan periode <span class="text-rose-500">*</span></label>
                        <select id="month" name="month" class="select @error('month') !border-rose-400 @enderror">
                            @foreach($monthNames as $number => $name)
                                <option value="{{ $number }}" @selected((string) $selectedMonth === (string) $number)>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('month')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="label">Deskripsi / isi laporan <span class="text-rose-500">*</span></label>
                    <textarea id="description" name="description" rows="9" required
                              placeholder="Uraikan kegiatan, capaian, kendala, dan rencana tindak lanjut sekretariat pada periode ini..."
                              class="textarea !leading-relaxed @error('description') !border-rose-400 !ring-rose-500/10 @enderror">{{ old('description', $editing ? $report->description : '') }}</textarea>
                    <div class="mt-1.5 flex items-start justify-between gap-3">
                        <p class="help-text !mt-0">Minimal 20 karakter. Gunakan bahasa yang jelas dan ringkas.</p>
                        <p class="shrink-0 text-[10px] font-semibold text-slate-400" data-char-count>0 karakter</p>
                    </div>
                    @error('description')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="anyflip_url" class="label">Tautan buku digital AnyFlip <span class="text-slate-400">(opsional)</span></label>
                    <input id="anyflip_url" name="anyflip_url" type="url" maxlength="2048"
                           value="{{ old('anyflip_url', $editing ? $report->anyflip_url : '') }}"
                           placeholder="https://anyflip.com/akun/kode/"
                           class="input @error('anyflip_url') !border-rose-400 !ring-rose-500/10 @enderror">
                    <p class="help-text">Unggah PDF laporan ke AnyFlip, lalu tempelkan URL publik buku digital di sini.</p>
                    @error('anyflip_url')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="card p-5 sm:p-6">
            <div class="mb-5 flex items-start gap-3">
                <span class="icon-tile tone-sky h-9 w-9 rounded-lg"><x-icon name="paperclip" class="h-4 w-4" /></span>
                <div>
                    <h2 class="text-[15px] font-extrabold text-ink-900">Lampiran Pendukung</h2>
                    <p class="mt-0.5 text-[11px] text-slate-500">Unggah dokumen pendukung laporan (opsional).</p>
                </div>
            </div>

            @if($editing && $report->attachments->count())
                <div class="mb-4 space-y-2">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Lampiran tersimpan</p>
                    @foreach($report->attachments as $attachment)
                        <div class="file-row">
                            <span class="icon-tile tone-{{ $attachment->icon_tone }} h-9 w-9 rounded-lg text-[9px] font-extrabold">{{ $attachment->extension }}</span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-xs font-semibold text-slate-700">{{ $attachment->file_name }}</p>
                                <p class="text-[10px] text-slate-400">{{ $attachment->human_size }} &middot; {{ $attachment->created_at->format('d M Y') }}</p>
                            </div>
                            <a href="{{ route('attachments.download', $attachment) }}" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-emerald-50 hover:text-emerald-600" title="Unduh">
                                <x-icon name="download" class="h-4 w-4" />
                            </a>
                            <form method="POST" action="{{ route('attachments.destroy', $attachment) }}" data-confirm="Hapus lampiran ini?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600" title="Hapus">
                                    <x-icon name="trash" class="h-4 w-4" />
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif

            <div data-upload>
                <label data-dropzone class="dropzone">
                    <input type="file" name="attachments[]" multiple class="sr-only" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" data-max-files="5" data-max-size="10485760">
                    <span class="mb-3 flex h-11 w-11 items-center justify-center rounded-xl bg-white text-emerald-600 shadow-sm ring-1 ring-slate-200">
                        <x-icon name="upload" class="h-5 w-5" />
                    </span>
                    <p class="text-[13px] font-bold text-slate-700">Klik atau seret berkas ke area ini</p>
                    <p class="mt-1 text-[11px] text-slate-400">PDF, DOC/DOCX, XLS/XLSX, JPG, PNG &middot; maks. 10 MB per file &middot; maks. 5 file</p>
                </label>
                <div data-file-list class="mt-3 space-y-2"></div>
            </div>
            @error('attachments')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
            @error('attachments.*')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
        </div>

        <div class="flex flex-col-reverse items-stretch gap-2 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ $editing ? route('reports.show', $report) : route('reports.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">
                <x-icon name="save" class="h-4 w-4" /> {{ $submitLabel }}
            </button>
        </div>
    </div>

    <div class="space-y-5">
        <div class="card p-5">
            <h3 class="text-[13px] font-extrabold text-ink-900">Alur Persetujuan</h3>
            <div class="mt-4 space-y-0">
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-100 text-emerald-600"><x-icon name="pencil" class="h-3.5 w-3.5" /></span>
                        <span class="my-1 h-8 w-px bg-slate-200"></span>
                    </div>
                    <div class="pb-3">
                        <p class="text-[12px] font-bold text-slate-800">Disusun</p>
                        <p class="text-[10px] leading-relaxed text-slate-400">Laporan diisi oleh staf sekretariat.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full {{ $editing && $report->status === 'pending' ? 'bg-amber-100 text-amber-600' : 'bg-slate-100 text-slate-400' }}"><x-icon name="clock" class="h-3.5 w-3.5" /></span>
                        <span class="my-1 h-8 w-px bg-slate-200"></span>
                    </div>
                    <div class="pb-3">
                        <p class="text-[12px] font-bold text-slate-800">Ditinjau</p>
                        <p class="text-[10px] leading-relaxed text-slate-400">Administrator memeriksa kelengkapan.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div>
                        <span class="flex h-7 w-7 items-center justify-center rounded-full {{ $editing && $report->status === 'approved' ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400' }}"><x-icon name="check" class="h-3.5 w-3.5" /></span>
                    </div>
                    <div>
                        <p class="text-[12px] font-bold text-slate-800">Diarsipkan</p>
                        <p class="text-[10px] leading-relaxed text-slate-400">Laporan disetujui dan siap dicetak.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-gradient-to-br from-emerald-50/80 to-teal-50/40 p-5">
            <div class="flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-600 text-white"><x-icon name="sparkles" class="h-3.5 w-3.5" /></span>
                <h3 class="text-[13px] font-extrabold text-emerald-900">Tips laporan berkualitas</h3>
            </div>
            <ul class="mt-3 space-y-2.5">
                @foreach([
                    'Gunakan judul yang spesifik dan menyebut periode.',
                    'Cantumkan capaian, kendala, dan tindak lanjut.',
                    'Lampirkan bukti pendukung bila diperlukan.',
                    'Periksa kembali sebelum laporan disubmit.',
                ] as $tip)
                    <li class="flex items-start gap-2 text-[11px] leading-relaxed text-emerald-800">
                        <x-icon name="check" class="mt-0.5 h-3 w-3 shrink-0 text-emerald-500" /> {{ $tip }}
                    </li>
                @endforeach
            </ul>
        </div>

        @if($editing)
            <div class="card p-5">
                <div class="flex items-center justify-between">
                    <h3 class="text-[13px] font-extrabold text-ink-900">Status Saat Ini</h3>
                    <x-status-badge :tone="$report->status_tone" :label="$report->status_label" />
                </div>
                <div class="mt-3 space-y-2 text-[11px]">
                    <div class="flex justify-between"><span class="text-slate-400">Dibuat</span><span class="font-semibold text-slate-600">{{ $report->created_at->format('d M Y') }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-400">Penyusun</span><span class="font-semibold text-slate-600">{{ $report->user?->name }}</span></div>
                    @if($report->reviewed_at)
                        <div class="flex justify-between"><span class="text-slate-400">Direview</span><span class="font-semibold text-slate-600">{{ $report->reviewed_at->format('d M Y') }}</span></div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</form>
