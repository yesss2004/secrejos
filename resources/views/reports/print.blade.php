<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $report->title }} &middot; Cetak</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Source+Serif+4:opsz,wght@8..60,400;8..60,600;8..60,700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: #e9edf3;
            color: #1f2937;
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 14px 24px;
            background: #0c1a2b;
            color: #fff;
        }
        .toolbar p { margin: 0; font-size: 12px; color: #94a3b8; }
        .toolbar strong { display: block; font-size: 13px; color: #fff; }
        .toolbar .actions { display: flex; gap: 8px; }
        .toolbar button, .toolbar a {
            border: 1px solid rgba(255,255,255,.18);
            background: rgba(255,255,255,.08);
            color: #fff;
            border-radius: 8px;
            padding: 9px 14px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            font-family: inherit;
        }
        .toolbar button.primary { background: #059669; border-color: #059669; }
        .paper { padding: 28px 16px 56px; }
        .sheet {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            padding: 18mm 19mm 16mm;
            box-shadow: 0 24px 60px -28px rgba(15,23,42,.45);
            position: relative;
        }
        .letterhead { display: flex; align-items: center; gap: 16px; border-bottom: 3px double #0f172a; padding-bottom: 14px; }
        .logo {
            width: 66px; height: 66px; flex: 0 0 auto; border-radius: 14px;
            background: linear-gradient(135deg, #0f766e, #0c1a2b);
            color: #fff; display: flex; align-items: center; justify-content: center;
            font-size: 28px; font-weight: 800; letter-spacing: -1px;
        }
        .letterhead .org { flex: 1; }
        .letterhead h1 { margin: 0; font-family: 'Source Serif 4', Georgia, serif; font-size: 21px; letter-spacing: .04em; text-transform: uppercase; }
        .letterhead h2 { margin: 2px 0 0; font-size: 10px; letter-spacing: .24em; text-transform: uppercase; color: #64748b; font-weight: 700; }
        .letterhead p { margin: 5px 0 0; font-size: 9px; color: #64748b; line-height: 1.5; }
        .doc-title { text-align: center; margin: 30px 0 6px; }
        .doc-title h2 { margin: 0; font-family: 'Source Serif 4', Georgia, serif; font-size: 19px; text-transform: uppercase; letter-spacing: .03em; text-decoration: underline; text-underline-offset: 5px; }
        .doc-title p { margin: 7px 0 0; font-size: 11px; color: #64748b; }
        table.meta { width: 100%; border-collapse: collapse; margin: 22px 0 24px; font-size: 11px; }
        table.meta td { padding: 8px 10px; border: 1px solid #e2e8f0; vertical-align: top; }
        table.meta td.label { width: 24%; background: #f8fafc; font-weight: 700; color: #475569; }
        .content { font-family: 'Source Serif 4', Georgia, serif; font-size: 12.5px; line-height: 1.9; text-align: justify; white-space: pre-line; color: #1f2937; }
        .section-label { font-size: 10px; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; color: #64748b; margin: 26px 0 8px; }
        .attachments { display: grid; gap: 6px; font-size: 11px; }
        .attachment { display: flex; justify-content: space-between; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 10px; }
        .attachment span:last-child { color: #64748b; }
        .signatures { display: flex; justify-content: space-between; gap: 40px; margin-top: 52px; page-break-inside: avoid; }
        .signature { width: 44%; text-align: center; font-size: 11px; line-height: 1.6; }
        .signature .space { height: 62px; }
        .signature .name { font-weight: 700; text-decoration: underline; text-underline-offset: 3px; }
        .signature .role { color: #64748b; font-size: 10px; }
        .stamp {
            position: absolute; right: 24mm; bottom: 32mm; width: 108px; height: 108px; border-radius: 50%;
            display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;
            transform: rotate(-13deg); opacity: .82; border: 3px solid currentColor; font-weight: 800;
            text-transform: uppercase; letter-spacing: .05em;
        }
        .stamp.approved { color: #059669; }
        .stamp.pending { color: #d97706; }
        .stamp.rejected { color: #e11d48; }
        .stamp span:first-child { font-size: 9px; }
        .stamp span:nth-child(2) { font-size: 13px; margin: 2px 0; }
        .stamp span:last-child { font-size: 8px; font-weight: 600; letter-spacing: .08em; }
        .footer { margin-top: 34px; padding-top: 10px; border-top: 1px solid #e2e8f0; font-size: 8.5px; color: #94a3b8; display: flex; justify-content: space-between; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none !important; }
            .paper { padding: 0; }
            .sheet { width: auto; min-height: auto; box-shadow: none; margin: 0; padding: 12mm 14mm; }
            @page { size: A4; margin: 0; }
        }
        @media (max-width: 820px) {
            .sheet { width: 100%; padding: 24px 20px; }
            .toolbar { padding: 12px 14px; }
            .stamp { display: none; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <div>
            <strong>{{ $report->title }}</strong>
            <p>Pratinjau dokumen cetak &middot; e-Secretariat</p>
        </div>
        <div class="actions">
            <a href="{{ route('reports.show', $report) }}">Kembali</a>
            <a href="https://anyflip.com/" target="_blank" rel="noopener noreferrer">Unggah ke AnyFlip</a>
            <button class="primary" onclick="window.print()">Cetak / Simpan PDF</button>
        </div>
    </div>

    <div class="paper">
        <div class="sheet">
            <div class="letterhead">
                <div class="logo">eS</div>
                <div class="org">
                    <h1>Sekretariat Utama</h1>
                    <h2>e-Secretariat &middot; Digital Office</h2>
                    <p>Jalan Administrasi Negara No. 17, Jakarta &middot; Telepon (021) 555-0177 &middot; sekretariat@instansi.go.id</p>
                </div>
            </div>

            <div class="doc-title">
                <h2>{{ $report->type_label === 'Tahunan' ? 'Laporan Tahunan' : 'Laporan Bulanan' }}</h2>
                <p>{{ $report->period_label }} &middot; Nomor Dokumen LAP-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</p>
            </div>

            <table class="meta">
                <tr>
                    <td class="label">Judul Laporan</td>
                    <td colspan="3"><strong>{{ $report->title }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Jenis</td>
                    <td>{{ $report->type_label }}</td>
                    <td class="label">Periode</td>
                    <td>{{ $report->period_label }}</td>
                </tr>
                <tr>
                    <td class="label">Penyusun</td>
                    <td>{{ $report->user?->name }}<br><span style="color:#64748b;font-size:9px">{{ $report->user?->position }}</span></td>
                    <td class="label">Tanggal Dibuat</td>
                    <td>{{ $report->created_at->locale('id')->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Status</td>
                    <td>{{ $report->status_label }}</td>
                    <td class="label">Reviewer</td>
                    <td>{{ $report->reviewer?->name ?? 'Belum ditinjau' }}</td>
                </tr>
            </table>

            <div class="section-label">Isi Laporan</div>
            <div class="content">{{ $report->description }}</div>

            @if($report->attachments->count())
                <div class="section-label">Lampiran</div>
                <div class="attachments">
                    @foreach($report->attachments as $index => $attachment)
                        <div class="attachment">
                            <span>{{ $index + 1 }}. {{ $attachment->file_name }}</span>
                            <span>{{ $attachment->human_size }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($report->review_note)
                <div class="section-label">Catatan Reviewer</div>
                <div class="content" style="font-style: italic; color: #475569;">{{ $report->review_note }}</div>
            @endif

            <div class="signatures">
                <div class="signature">
                    <p>Disusun oleh,</p>
                    <div class="space"></div>
                    <p class="name">{{ $report->user?->name }}</p>
                    <p class="role">{{ $report->user?->position ?? 'Staf Sekretariat' }}</p>
                </div>
                <div class="signature">
                    <p>{{ $report->status === 'approved' ? 'Disetujui oleh,' : 'Mengetahui,' }}</p>
                    <div class="space"></div>
                    <p class="name">{{ $report->reviewer?->name ?? 'Kepala Sekretariat' }}</p>
                    <p class="role">{{ $report->reviewer?->position ?? 'Kepala Sekretariat' }}</p>
                </div>
            </div>

            <div class="stamp {{ $report->status }}">
                <span>Status Dokumen</span>
                <span>{{ $report->status === 'approved' ? 'Disetujui' : ($report->status === 'rejected' ? 'Ditolak' : 'Menunggu') }}</span>
                <span>{{ $report->reviewed_at?->format('d/m/Y') ?? now()->format('d/m/Y') }}</span>
            </div>

            <div class="footer">
                <span>Dokumen ini dihasilkan secara elektronik oleh e-Secretariat.</span>
                <span>Dicetak {{ now()->locale('id')->translatedFormat('d F Y, H:i') }} WIB</span>
            </div>
        </div>
    </div>
</body>
</html>
