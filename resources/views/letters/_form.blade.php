@php
    $editing = isset($letter) && $letter;
    $selectedType = old('type', $editing ? $letter->type : ($suggestedType ?? 'masuk'));
    $selectedStatus = old('status', $editing ? $letter->status : 'baru');
    $referenceValue = old('reference_number', $editing ? $letter->reference_number : ($suggestedReference ?? ''));
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="grid grid-cols-1 gap-5 xl:grid-cols-3">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="space-y-5 xl:col-span-2">
        <div class="card p-5 sm:p-6">
            <div class="mb-5 flex items-start gap-3">
                <span class="icon-tile tone-sky h-9 w-9 rounded-lg"><x-icon name="mail" class="h-4 w-4" /></span>
                <div>
                    <h2 class="text-[15px] font-extrabold text-ink-900">Informasi Surat</h2>
                    <p class="mt-0.5 text-[11px] text-slate-500">Catat identitas dan detail surat ke dalam arsip digital.</p>
                </div>
            </div>

            <div class="space-y-5">
                <div>
                    <span class="label">Arah surat <span class="text-rose-500">*</span></span>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        @foreach([
                            ['value' => 'masuk', 'label' => 'Surat Masuk', 'desc' => 'Diterima dari pihak eksternal', 'icon' => 'inbox', 'tone' => 'tone-sky'],
                            ['value' => 'keluar', 'label' => 'Surat Keluar', 'desc' => 'Dikirim oleh sekretariat', 'icon' => 'send', 'tone' => 'tone-violet'],
                        ] as $option)
                            <label class="relative cursor-pointer">
                                <input type="radio" name="type" value="{{ $option['value'] }}" class="peer sr-only" @checked($selectedType === $option['value'])>
                                <div class="radio-card flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-3.5 transition hover:border-slate-300 peer-checked:border-emerald-500 peer-checked:bg-emerald-50/50 peer-checked:ring-1 peer-checked:ring-emerald-500">
                                    <span class="icon-tile h-9 w-9 rounded-lg {{ $option['tone'] }}"><x-icon :name="$option['icon']" class="h-4 w-4" /></span>
                                    <div class="flex-1">
                                        <p class="text-[12px] font-bold text-slate-800">{{ $option['label'] }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $option['desc'] }}</p>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('type')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="reference_number" class="label">Nomor surat <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input id="reference_number" name="reference_number" type="text" value="{{ $referenceValue }}" required maxlength="80"
                                   placeholder="SM/001/XI/2026"
                                   class="input !font-mono !text-[13px] @error('reference_number') !border-rose-400 @enderror">
                            @if($referenceValue)
                                <button type="button" data-copy="{{ $referenceValue }}" class="absolute right-2 top-1/2 -translate-y-1/2 rounded-md px-2 py-1 text-[10px] font-bold text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">Salin</button>
                            @endif
                        </div>
                        <p class="help-text">Nomor otomatis disarankan, dapat disesuaikan.</p>
                        @error('reference_number')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="classification" class="label">Klasifikasi <span class="text-rose-500">*</span></label>
                        <select id="classification" name="classification" required class="select @error('classification') !border-rose-400 @enderror">
                            @foreach($classifications as $classification)
                                <option value="{{ $classification }}" @selected(old('classification', $editing ? $letter->classification : '') === $classification)>{{ $classification }}</option>
                            @endforeach
                        </select>
                        @error('classification')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="title" class="label">Judul / perihal surat <span class="text-rose-500">*</span></label>
                    <input id="title" name="title" type="text" value="{{ old('title', $editing ? $letter->title : '') }}" required maxlength="160"
                           placeholder="Contoh: Undangan Rapat Koordinasi Pimpinan"
                           class="input @error('title') !border-rose-400 @enderror">
                    @error('title')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="correspondent" class="label">Pengirim / penerima <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <x-icon name="building" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <input id="correspondent" name="correspondent" type="text" value="{{ old('correspondent', $editing ? $letter->correspondent : '') }}" required maxlength="160"
                               placeholder="Nama instansi atau pihak terkait"
                               class="input !pl-10 @error('correspondent') !border-rose-400 @enderror">
                    </div>
                    @error('correspondent')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label for="letter_date" class="label">Tanggal surat <span class="text-rose-500">*</span></label>
                        <input id="letter_date" name="letter_date" type="date" value="{{ old('letter_date', $editing ? $letter->letter_date->format('Y-m-d') : now()->format('Y-m-d')) }}" required
                               class="input @error('letter_date') !border-rose-400 @enderror">
                        @error('letter_date')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="received_date" class="label">Tanggal diterima</label>
                        <input id="received_date" name="received_date" type="date" value="{{ old('received_date', $editing && $letter->received_date ? $letter->received_date->format('Y-m-d') : '') }}"
                               class="input @error('received_date') !border-rose-400 @enderror">
                        <p class="help-text">Khusus surat masuk.</p>
                        @error('received_date')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="status" class="label">Status <span class="text-rose-500">*</span></label>
                        <select id="status" name="status" required class="select @error('status') !border-rose-400 @enderror">
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" @selected($selectedStatus === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="label">Ringkasan / keterangan</label>
                    <textarea id="description" name="description" rows="5"
                              placeholder="Tuliskan ringkasan isi surat atau tindak lanjut yang diperlukan..."
                              class="textarea !leading-relaxed @error('description') !border-rose-400 @enderror">{{ old('description', $editing ? $letter->description : '') }}</textarea>
                    @error('description')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="card p-5 sm:p-6">
            <div class="mb-4 flex items-start gap-3">
                <span class="icon-tile tone-amber h-9 w-9 rounded-lg"><x-icon name="paperclip" class="h-4 w-4" /></span>
                <div>
                    <h2 class="text-[15px] font-extrabold text-ink-900">Berkas Surat</h2>
                    <p class="mt-0.5 text-[11px] text-slate-500">Unggah hasil pindai atau dokumen digital surat (opsional).</p>
                </div>
            </div>

            @if($editing && $letter->file_path)
                <div class="file-row mb-3">
                    <span class="icon-tile tone-rose h-9 w-9 rounded-lg text-[9px] font-extrabold">PDF</span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-xs font-semibold text-slate-700">{{ $letter->file_name }}</p>
                        <p class="text-[10px] text-slate-400">{{ $letter->human_size }} &middot; berkas saat ini</p>
                    </div>
                    <a href="{{ route('letters.download', $letter) }}" class="btn-secondary btn-sm"><x-icon name="download" class="h-3.5 w-3.5" /> Unduh</a>
                </div>
            @endif

            <label class="dropzone">
                <input id="letter-file" type="file" name="file" class="sr-only" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" data-file-label>
                <span class="mb-3 flex h-11 w-11 items-center justify-center rounded-xl bg-white text-emerald-600 shadow-sm ring-1 ring-slate-200">
                    <x-icon name="upload" class="h-5 w-5" />
                </span>
                <p class="text-[13px] font-bold text-slate-700">Klik untuk memilih berkas</p>
                <p class="mt-1 text-[11px] text-slate-400">PDF, DOC/DOCX, XLS/XLSX, JPG, PNG &middot; maks. 10 MB</p>
                <p class="mt-2 text-[11px] font-semibold text-emerald-700" data-file-name="letter-file">Belum ada berkas dipilih</p>
            </label>
            @error('file')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
        </div>

        <div class="flex flex-col-reverse items-stretch gap-2 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ $editing ? route('letters.show', $letter) : route('letters.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">
                <x-icon name="save" class="h-4 w-4" /> {{ $submitLabel }}
            </button>
        </div>
    </div>

    <div class="space-y-5">
        <div class="card p-5">
            <h3 class="text-[13px] font-extrabold text-ink-900">Panduan Penomoran</h3>
            <div class="mt-3 rounded-xl bg-slate-50 p-3.5 font-mono text-[12px] font-bold text-slate-700 ring-1 ring-inset ring-slate-200/70">
                {{ $selectedType === 'keluar' ? 'SK' : 'SM' }}/001/{{ strtoupper(now()->locale('id')->translatedFormat('M')) }}/{{ now()->year }}
            </div>
            <div class="mt-3 space-y-2 text-[10px] leading-relaxed text-slate-500">
                <p><span class="font-bold text-slate-700">SM</span> &mdash; Surat Masuk</p>
                <p><span class="font-bold text-slate-700">SK</span> &mdash; Surat Keluar</p>
                <p>Urutan nomor disarankan otomatis berdasarkan jumlah surat sejenis. Pastikan nomor unik dan sesuai buku agenda.</p>
            </div>
        </div>

        <div class="card p-5">
            <h3 class="text-[13px] font-extrabold text-ink-900">Alur Tindak Lanjut</h3>
            <div class="mt-4 space-y-0">
                @foreach([
                    ['label' => 'Surat dicatat', 'desc' => 'Registrasi nomor dan detail surat.', 'icon' => 'pencil', 'done' => true],
                    ['label' => 'Diproses', 'desc' => 'Diteruskan ke unit terkait.', 'icon' => 'refresh', 'done' => in_array($selectedStatus, ['diproses', 'selesai', 'arsip'])],
                    ['label' => 'Selesai', 'desc' => 'Tindak lanjut telah tuntas.', 'icon' => 'check', 'done' => in_array($selectedStatus, ['selesai', 'arsip'])],
                    ['label' => 'Diarsipkan', 'desc' => 'Disimpan pada arsip digital.', 'icon' => 'archive', 'done' => $selectedStatus === 'arsip'],
                ] as $index => $step)
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <span class="flex h-7 w-7 items-center justify-center rounded-full {{ $step['done'] ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400' }}">
                                <x-icon :name="$step['icon']" class="h-3.5 w-3.5" />
                            </span>
                            @if(! $loop->last)<span class="my-1 h-6 w-px bg-slate-200"></span>@endif
                        </div>
                        <div class="{{ $loop->last ? '' : 'pb-3' }}">
                            <p class="text-[12px] font-bold text-slate-800">{{ $step['label'] }}</p>
                            <p class="text-[10px] leading-relaxed text-slate-400">{{ $step['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card bg-gradient-to-br from-sky-50/80 to-indigo-50/30 p-5">
            <div class="flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-sky-600 text-white"><x-icon name="info" class="h-3.5 w-3.5" /></span>
                <h3 class="text-[13px] font-extrabold text-sky-900">Tips pengarsipan</h3>
            </div>
            <ul class="mt-3 space-y-2">
                @foreach([
                    'Pastikan nomor surat sesuai buku agenda.',
                    'Gunakan judul yang menggambarkan isi surat.',
                    'Unggah berkas pindai agar mudah ditelusuri.',
                    'Perbarui status setelah tindak lanjut selesai.',
                ] as $tip)
                    <li class="flex items-start gap-2 text-[11px] leading-relaxed text-sky-800">
                        <x-icon name="check" class="mt-0.5 h-3 w-3 shrink-0 text-sky-500" /> {{ $tip }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</form>
