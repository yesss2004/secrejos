@php
    $editing = isset($plan) && $plan;
    $data = old('plan', $editing ? $plan->only([
        'year', 'organization_unit', 'sub_organization_unit', 'program_code', 'program_name',
        'activity_code', 'activity_name', 'sub_activity_code', 'sub_activity_name', 'spm',
        'service_type', 'funding_source', 'location', 'implementation_time', 'target_group', 'output',
    ]) : []);
    $indicatorRows = old('indicators', $editing ? $plan->indicators->toArray() : [[
        'category' => '', 'indicator_before' => '', 'benchmark_before' => '', 'target_before' => '',
        'indicator_after' => '', 'benchmark_after' => '', 'target_after' => '',
    ]]);
    $budgetRows = old('budget_lines', $editing ? $plan->budgetLines->toArray() : [[
        'account_code' => '', 'description' => '', 'coefficient_before' => 0, 'unit_before' => '',
        'unit_price_before' => 0, 'tax_before' => 0, 'coefficient_after' => 0, 'unit_after' => '',
        'unit_price_after' => 0, 'tax_after' => 0,
    ]]);
    if (count($indicatorRows) === 0) $indicatorRows = [[]];
    if (count($budgetRows) === 0) $budgetRows = [[]];
@endphp

<form method="POST" action="{{ $action }}" class="space-y-5">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800" role="alert">
            <p class="font-bold">Mohon periksa kembali isian berikut:</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <p class="mt-2 text-xs">Semua nilai anggaran diisi dalam Rupiah.</p>
        </div>
    @endif

    <section class="card p-5 sm:p-6">
        <div class="mb-5">
            <h2 class="text-[15px] font-extrabold text-ink-900">Identitas Subkegiatan</h2>
            <p class="mt-1 text-[11px] text-slate-500">Masukkan informasi kegiatan dan sumber pendanaannya.</p>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach([
                ['year', 'Tahun Anggaran', 'number', true],
                ['organization_unit', 'Unit Organisasi', 'text', true],
                ['sub_organization_unit', 'Sub Unit Organisasi', 'text', false],
                ['program_code', 'Kode Program', 'text', false],
                ['program_name', 'Program', 'text', true],
                ['activity_code', 'Kode Kegiatan', 'text', false],
                ['activity_name', 'Kegiatan', 'text', true],
                ['sub_activity_code', 'Kode Subkegiatan', 'text', true],
                ['sub_activity_name', 'Subkegiatan', 'text', true],
                ['spm', 'SPM', 'text', false],
                ['service_type', 'Jenis Layanan', 'text', false],
                ['funding_source', 'Sumber Pendanaan', 'text', false],
                ['location', 'Lokasi', 'text', false],
                ['implementation_time', 'Waktu Pelaksanaan', 'text', false],
                ['target_group', 'Kelompok Sasaran', 'text', false],
                ['output', 'Keluaran Subkegiatan', 'text', false],
            ] as [$field, $label, $type, $required])
                <div>
                    <label for="plan_{{ $field }}" class="label">{{ $label }} @if($required)<span class="text-rose-500">*</span>@endif</label>
                    <input id="plan_{{ $field }}" name="plan[{{ $field }}]" type="{{ $type }}"
                           value="{{ old('plan.'.$field, $data[$field] ?? ($field === 'year' ? now()->year : '')) }}"
                           @if($type === 'number') min="2000" max="{{ now()->year + 5 }}" @endif
                           @if($required) required @endif maxlength="{{ $field === 'year' ? 4 : 200 }}"
                           class="input @error('plan.'.$field) !border-rose-400 @enderror">
                    @error('plan.'.$field)<p class="field-error">{{ $message }}</p>@enderror
                </div>
            @endforeach
        </div>
    </section>

    <section class="card overflow-hidden">
        <div class="flex flex-col gap-3 border-b border-slate-100 p-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 class="text-[15px] font-extrabold text-ink-900">Indikator dan Tolok Ukur Kinerja</h2>
                <p class="mt-1 text-[11px] text-slate-500">Bandingkan indikator, tolok ukur, dan target sebelum/sesudah.</p>
            </div>
            <button type="button" class="btn-secondary btn-sm self-start" data-add-row="indicator">
                <x-icon name="plus" class="h-3.5 w-3.5" /> Tambah Indikator
            </button>
        </div>
        <div id="indicator-rows" class="space-y-4 p-5 sm:p-6">
            @foreach($indicatorRows as $index => $row)
                <div class="rounded-xl border border-slate-200 p-4" data-row>
                    <div class="mb-3 flex items-center justify-between">
                        <p class="text-xs font-bold text-slate-700">Indikator <span data-row-number>{{ $loop->iteration }}</span></p>
                        <button type="button" class="btn-ghost btn-sm text-rose-600" data-remove-row aria-label="Hapus indikator">Hapus</button>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                        @foreach([
                            ['category', 'Kelompok', 'Contoh: Keluaran'],
                            ['indicator_before', 'Indikator Sebelum', 'Nama indikator'],
                            ['benchmark_before', 'Tolok Ukur Sebelum', 'Uraian tolok ukur'],
                            ['target_before', 'Target Sebelum', 'Contoh: 2 Dokumen'],
                            ['indicator_after', 'Indikator Sesudah', 'Nama indikator'],
                            ['benchmark_after', 'Tolok Ukur Sesudah', 'Uraian tolok ukur'],
                            ['target_after', 'Target Sesudah', 'Contoh: 2 Dokumen'],
                        ] as [$field, $label, $placeholder])
                            <div>
                                <label class="label" for="indicator_{{ $index }}_{{ $field }}">{{ $label }} <span class="text-rose-500">*</span></label>
                                <input id="indicator_{{ $index }}_{{ $field }}" name="indicators[{{ $index }}][{{ $field }}]"
                                       value="{{ old("indicators.$index.$field", $row[$field] ?? '') }}" required maxlength="1000"
                                       placeholder="{{ $placeholder }}" class="input">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="card overflow-hidden">
        <div class="flex flex-col gap-3 border-b border-slate-100 p-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 class="text-[15px] font-extrabold text-ink-900">Rincian Anggaran Belanja</h2>
                <p class="mt-1 text-[11px] text-slate-500">Total dihitung otomatis dari koefisien × harga + PPN.</p>
            </div>
            <button type="button" class="btn-secondary btn-sm self-start" data-add-row="budget">
                <x-icon name="plus" class="h-3.5 w-3.5" /> Tambah Baris Anggaran
            </button>
        </div>
        <div id="budget-rows" class="space-y-4 p-5 sm:p-6">
            @foreach($budgetRows as $index => $row)
                <div class="rounded-xl border border-slate-200 p-4" data-row>
                    <div class="mb-3 flex items-center justify-between">
                        <p class="text-xs font-bold text-slate-700">Rincian <span data-row-number>{{ $loop->iteration }}</span></p>
                        <button type="button" class="btn-ghost btn-sm text-rose-600" data-remove-row aria-label="Hapus rincian anggaran">Hapus</button>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="label" for="budget_{{ $index }}_account_code">Kode Rekening</label>
                            <input id="budget_{{ $index }}_account_code" name="budget_lines[{{ $index }}][account_code]" value="{{ old("budget_lines.$index.account_code", $row['account_code'] ?? '') }}" maxlength="60" class="input">
                        </div>
                        <div>
                            <label class="label" for="budget_{{ $index }}_description">Uraian <span class="text-rose-500">*</span></label>
                            <input id="budget_{{ $index }}_description" name="budget_lines[{{ $index }}][description]" value="{{ old("budget_lines.$index.description", $row['description'] ?? '') }}" required maxlength="2000" class="input">
                        </div>
                    </div>
                    <div class="mt-4 grid gap-4 xl:grid-cols-2">
                        @foreach(['before' => 'Sebelum', 'after' => 'Sesudah'] as $side => $sideLabel)
                            <div class="rounded-lg bg-slate-50 p-3">
                                <p class="mb-3 text-[11px] font-extrabold uppercase tracking-wide text-slate-500">Rincian {{ $sideLabel }}</p>
                                <div class="grid grid-cols-2 gap-3">
                                    @foreach([
                                        ["coefficient_$side", 'Koefisien', 'number'],
                                        ["unit_$side", 'Satuan', 'text'],
                                        ["unit_price_$side", 'Harga Satuan (Rp)', 'number'],
                                        ["tax_$side", 'PPN (Rp)', 'number'],
                                    ] as [$field, $label, $type])
                                        <div>
                                            <label class="label" for="budget_{{ $index }}_{{ $field }}">{{ $label }} @if($type === 'number')<span class="text-rose-500">*</span>@endif</label>
                                            <input id="budget_{{ $index }}_{{ $field }}" name="budget_lines[{{ $index }}][{{ $field }}]"
                                                   value="{{ old("budget_lines.$index.$field", $row[$field] ?? ($type === 'number' ? 0 : '')) }}"
                                                   type="{{ $type }}" @if($type === 'number') min="0" step="0.01" data-budget-input="{{ $side }}" @endif
                                                   @if($type === 'number') required @endif maxlength="80"
                                                   class="input @if($type === 'number') text-right tabular-nums @endif">
                                        </div>
                                    @endforeach
                                </div>
                                <p class="mt-3 text-right text-xs font-bold text-slate-600">Jumlah {{ $sideLabel }}: <span data-line-total="{{ $side }}">Rp0</span></p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        <div class="border-t border-slate-100 bg-slate-50/70 px-5 py-4 sm:px-6">
            <div class="grid gap-2 text-xs sm:ml-auto sm:max-w-xl sm:grid-cols-3 sm:gap-3 sm:text-right">
                <div><p class="text-slate-400">Total Sebelum</p><p class="mt-1 font-extrabold text-slate-700" data-grand-total="before">Rp0</p></div>
                <div><p class="text-slate-400">Total Sesudah</p><p class="mt-1 font-extrabold text-emerald-700" data-grand-total="after">Rp0</p></div>
                <div><p class="text-slate-400">Selisih</p><p class="mt-1 font-extrabold text-slate-700" data-grand-total="difference">Rp0</p></div>
            </div>
        </div>
    </section>

    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-between">
        <a href="{{ $editing ? route('plans.show', $plan) : route('plans.index') }}" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary"><x-icon name="save" class="h-4 w-4" /> {{ $submitLabel }}</button>
    </div>
</form>

<template id="indicator-template">
    <div class="rounded-xl border border-slate-200 p-4" data-row>
        <div class="mb-3 flex items-center justify-between">
            <p class="text-xs font-bold text-slate-700">Indikator <span data-row-number></span></p>
            <button type="button" class="btn-ghost btn-sm text-rose-600" data-remove-row>Hapus</button>
        </div>
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @foreach([
                ['category', 'Kelompok'], ['indicator_before', 'Indikator Sebelum'], ['benchmark_before', 'Tolok Ukur Sebelum'],
                ['target_before', 'Target Sebelum'], ['indicator_after', 'Indikator Sesudah'],
                ['benchmark_after', 'Tolok Ukur Sesudah'], ['target_after', 'Target Sesudah'],
            ] as [$field, $label])
                <div><label class="label">{{ $label }} <span class="text-rose-500">*</span></label>
                    <input name="indicators[__INDEX__][{{ $field }}]" required maxlength="1000" class="input"></div>
            @endforeach
        </div>
    </div>
</template>

<template id="budget-template">
    <div class="rounded-xl border border-slate-200 p-4" data-row>
        <div class="mb-3 flex items-center justify-between">
            <p class="text-xs font-bold text-slate-700">Rincian <span data-row-number></span></p>
            <button type="button" class="btn-ghost btn-sm text-rose-600" data-remove-row>Hapus</button>
        </div>
        <div class="grid gap-3 sm:grid-cols-2">
            <div><label class="label">Kode Rekening</label><input name="budget_lines[__INDEX__][account_code]" maxlength="60" class="input"></div>
            <div><label class="label">Uraian <span class="text-rose-500">*</span></label><input name="budget_lines[__INDEX__][description]" required maxlength="2000" class="input"></div>
        </div>
        <div class="mt-4 grid gap-4 xl:grid-cols-2">
            @foreach(['before' => 'Sebelum', 'after' => 'Sesudah'] as $side => $sideLabel)
                <div class="rounded-lg bg-slate-50 p-3">
                    <p class="mb-3 text-[11px] font-extrabold uppercase tracking-wide text-slate-500">Rincian {{ $sideLabel }}</p>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach([
                            ["coefficient_$side", 'Koefisien', 'number', '0'],
                            ["unit_$side", 'Satuan', 'text', ''],
                            ["unit_price_$side", 'Harga Satuan (Rp)', 'number', '0'],
                            ["tax_$side", 'PPN (Rp)', 'number', '0'],
                        ] as [$field, $label, $type, $default])
                            <div><label class="label">{{ $label }} @if($type === 'number')<span class="text-rose-500">*</span>@endif</label>
                                <input name="budget_lines[__INDEX__][{{ $field }}]" value="{{ $default }}" type="{{ $type }}"
                                       @if($type === 'number') min="0" step="0.01" data-budget-input="{{ $side }}" required @endif
                                       maxlength="80" class="input @if($type === 'number') text-right tabular-nums @endif"></div>
                        @endforeach
                    </div>
                    <p class="mt-3 text-right text-xs font-bold text-slate-600">Jumlah {{ $sideLabel }}: <span data-line-total="{{ $side }}">Rp0</span></p>
                </div>
            @endforeach
        </div>
    </div>
</template>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const money = (value) => new Intl.NumberFormat('id-ID', {
                style: 'currency', currency: 'IDR', maximumFractionDigits: 2,
            }).format(value || 0);
            const budgetRows = document.getElementById('budget-rows');
            const indicatorRows = document.getElementById('indicator-rows');

            const refreshTotals = () => {
                let beforeTotal = 0;
                let afterTotal = 0;
                budgetRows.querySelectorAll('[data-row]').forEach((row) => {
                    const value = (side, field) => Number(row.querySelector(`[name*="[${field}_${side}]"]`)?.value || 0);
                    const before = value('before', 'coefficient') * value('before', 'unit_price') + value('before', 'tax');
                    const after = value('after', 'coefficient') * value('after', 'unit_price') + value('after', 'tax');
                    beforeTotal += before;
                    afterTotal += after;
                    row.querySelector('[data-line-total="before"]').textContent = money(before);
                    row.querySelector('[data-line-total="after"]').textContent = money(after);
                });
                document.querySelector('[data-grand-total="before"]').textContent = money(beforeTotal);
                document.querySelector('[data-grand-total="after"]').textContent = money(afterTotal);
                document.querySelector('[data-grand-total="difference"]').textContent = money(afterTotal - beforeTotal);
            };

            const refreshRowControls = () => {
                [indicatorRows, budgetRows].forEach((container) => {
                    const rows = [...container.querySelectorAll('[data-row]')];
                    rows.forEach((row, index) => {
                        row.querySelector('[data-row-number]').textContent = index + 1;
                        row.querySelector('[data-remove-row]').disabled = rows.length === 1;
                    });
                });
                refreshTotals();
            };

            document.querySelectorAll('[data-add-row]').forEach((button) => {
                button.addEventListener('click', () => {
                    const type = button.dataset.addRow;
                    const container = type === 'indicator' ? indicatorRows : budgetRows;
                    const template = document.getElementById(`${type}-template`);
                    const nextIndex = Math.max(-1, ...[...container.querySelectorAll('[name]')].map((input) => {
                        const match = input.name.match(/\[(\d+)\]/);
                        return match ? Number(match[1]) : -1;
                    })) + 1;
                    const html = template.innerHTML.split('__INDEX__').join(String(nextIndex));
                    container.insertAdjacentHTML('beforeend', html);
                    refreshRowControls();
                });
            });

            document.addEventListener('click', (event) => {
                const button = event.target.closest('[data-remove-row]');
                if (!button || button.disabled) return;
                button.closest('[data-row]').remove();
                refreshRowControls();
            });

            document.addEventListener('input', (event) => {
                if (event.target.matches('[data-budget-input]')) refreshTotals();
            });

            refreshRowControls();
        });
    </script>
@endpush
