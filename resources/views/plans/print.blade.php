<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perencanaan {{ $plan->sub_activity_code }} - {{ $plan->year }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #e8edf2; color: #172033; font: 10px Arial, sans-serif; }
        .toolbar { position: sticky; top: 0; display: flex; justify-content: space-between; align-items: center; padding: 12px 20px; background: #10233b; color: #fff; }
        .toolbar strong { font-size: 13px; }
        .toolbar button, .toolbar a { margin-left: 6px; border: 1px solid #53667c; border-radius: 6px; padding: 8px 12px; color: #fff; background: transparent; text-decoration: none; font-weight: 700; cursor: pointer; }
        .toolbar button { background: #059669; border-color: #059669; }
        .page { width: 297mm; min-height: 210mm; margin: 18px auto; padding: 12mm; background: #fff; box-shadow: 0 10px 35px #0f172a25; }
        h1 { margin: 0; text-align: center; font-size: 15px; text-transform: uppercase; }
        .subtitle { margin: 5px 0 18px; text-align: center; color: #475569; font-size: 11px; }
        h2 { margin: 18px 0 6px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #737b86; padding: 5px; vertical-align: top; overflow-wrap: anywhere; }
        th { background: #f0f3f6; text-align: center; font-weight: 700; }
        .meta td:first-child { width: 22%; font-weight: 700; background: #f6f7f8; }
        .indicators { font-size: 9px; }
        .budget { font-size: 8px; }
        .budget th, .budget td { padding: 4px 3px; }
        .numeric { text-align: right; white-space: nowrap; }
        tfoot th { background: #eef2f5; }
        .signature { display: flex; justify-content: flex-end; margin-top: 35px; text-align: center; }
        .signature > div { width: 220px; }
        .signature .space { height: 55px; }
        .signature strong { text-decoration: underline; }
        @media print {
            @page { size: A4 landscape; margin: 8mm; }
            body { background: #fff; }
            .toolbar { display: none; }
            .page { width: auto; min-height: auto; margin: 0; padding: 0; box-shadow: none; }
            h2 { break-after: avoid; }
            tr { break-inside: avoid; }
        }
        @media screen and (max-width: 1000px) {
            .page { width: 100%; margin: 0; padding: 18px; }
            .toolbar { position: static; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <strong>Pratinjau cetak &middot; {{ $plan->sub_activity_code }}</strong>
        <div><a href="{{ route('plans.show', $plan) }}">Kembali</a><button type="button" onclick="window.print()">Cetak / Simpan PDF</button></div>
    </div>
    <main class="page">
        <h1>Dokumen Perencanaan Subkegiatan</h1>
        <p class="subtitle">Tahun Anggaran {{ $plan->year }}</p>

        <table class="meta">
            <tr><td>Unit Organisasi</td><td>{{ $plan->organization_unit }}</td><td>Sub Unit Organisasi</td><td>{{ $plan->sub_organization_unit ?: '—' }}</td></tr>
            <tr><td>Program</td><td>{{ trim(($plan->program_code ? $plan->program_code.' ' : '').$plan->program_name) }}</td><td>Kegiatan</td><td>{{ trim(($plan->activity_code ? $plan->activity_code.' ' : '').$plan->activity_name) }}</td></tr>
            <tr><td>Subkegiatan</td><td colspan="3">{{ $plan->sub_activity_code }} {{ $plan->sub_activity_name }}</td></tr>
            <tr><td>SPM / Jenis Layanan</td><td>{{ $plan->spm ?: '—' }} / {{ $plan->service_type ?: '—' }}</td><td>Sumber Pendanaan</td><td>{{ $plan->funding_source ?: '—' }}</td></tr>
            <tr><td>Lokasi</td><td>{{ $plan->location ?: '—' }}</td><td>Waktu Pelaksanaan</td><td>{{ $plan->implementation_time ?: '—' }}</td></tr>
            <tr><td>Kelompok Sasaran</td><td>{{ $plan->target_group ?: '—' }}</td><td>Keluaran Subkegiatan</td><td>{{ $plan->output ?: '—' }}</td></tr>
        </table>

        <h2>Indikator dan Tolok Ukur Kinerja Kegiatan</h2>
        <table class="indicators">
            <thead><tr><th rowspan="2">Kelompok</th><th colspan="3">Sebelum</th><th colspan="3">Sesudah</th></tr><tr><th>Indikator</th><th>Tolok Ukur Kinerja</th><th>Target Kinerja</th><th>Indikator</th><th>Tolok Ukur Kinerja</th><th>Target Kinerja</th></tr></thead>
            <tbody>
                @foreach($plan->indicators as $indicator)
                    <tr><td>{{ $indicator->category }}</td><td>{{ $indicator->indicator_before }}</td><td>{{ $indicator->benchmark_before }}</td><td>{{ $indicator->target_before }}</td><td>{{ $indicator->indicator_after }}</td><td>{{ $indicator->benchmark_after }}</td><td>{{ $indicator->target_after }}</td></tr>
                @endforeach
            </tbody>
        </table>

        <h2>Rincian Anggaran Belanja Subkegiatan</h2>
        <table class="budget">
            <thead><tr>
                <th rowspan="2" style="width:7%">Kode Rekening</th><th rowspan="2" style="width:17%">Uraian</th>
                <th colspan="5">Rincian Perhitungan Sebelum</th><th colspan="5">Rincian Perhitungan Sesudah</th><th rowspan="2" style="width:7%">Bertambah / (Berkurang)</th>
            </tr><tr><th>Koef.</th><th>Satuan</th><th>Harga (Rp)</th><th>PPN (Rp)</th><th>Jumlah (Rp)</th><th>Koef.</th><th>Satuan</th><th>Harga (Rp)</th><th>PPN (Rp)</th><th>Jumlah (Rp)</th></tr></thead>
            <tbody>
                @foreach($plan->budgetLines as $line)
                    <tr>
                        <td>{{ $line->account_code ?: '—' }}</td><td>{{ $line->description }}</td>
                        <td class="numeric">{{ number_format($line->coefficient_before, 2, ',', '.') }}</td><td>{{ $line->unit_before ?: '—' }}</td>
                        <td class="numeric">{{ number_format($line->unit_price_before, 2, ',', '.') }}</td><td class="numeric">{{ number_format($line->tax_before, 2, ',', '.') }}</td><td class="numeric">{{ number_format($line->before_total, 2, ',', '.') }}</td>
                        <td class="numeric">{{ number_format($line->coefficient_after, 2, ',', '.') }}</td><td>{{ $line->unit_after ?: '—' }}</td>
                        <td class="numeric">{{ number_format($line->unit_price_after, 2, ',', '.') }}</td><td class="numeric">{{ number_format($line->tax_after, 2, ',', '.') }}</td><td class="numeric">{{ number_format($line->after_total, 2, ',', '.') }}</td>
                        <td class="numeric">{{ number_format($line->difference, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot><tr><th colspan="6" class="numeric">Jumlah Anggaran Sebelum</th><th class="numeric">{{ number_format($plan->total_before, 2, ',', '.') }}</th><th colspan="4" class="numeric">Jumlah Anggaran Sesudah</th><th class="numeric">{{ number_format($plan->total_after, 2, ',', '.') }}</th><th class="numeric">{{ number_format($plan->total_after - $plan->total_before, 2, ',', '.') }}</th></tr></tfoot>
        </table>

        <div class="signature"><div><p>{{ $plan->organization_unit }}, {{ now()->locale('id')->translatedFormat('d F Y') }}</p><p>Disusun oleh,</p><div class="space"></div><strong>{{ $plan->user?->name }}</strong></div></div>
    </main>
</body>
</html>
