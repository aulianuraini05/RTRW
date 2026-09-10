<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Surat Pengantar - {{ $letter->letter_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Times New Roman', Times, serif; color: #111; margin: 0; background: #f3f4f6; }
        .toolbar { max-width: 210mm; margin: 16px auto 0; padding: 0 16px; display: flex; gap: 12px; align-items: center; font-family: ui-sans-serif, system-ui, sans-serif; }
        .toolbar a, .toolbar button { font-size: 14px; padding: 8px 16px; border-radius: 6px; border: 1px solid #d1d5db; background: #fff; cursor: pointer; text-decoration: none; color: #111; }
        .toolbar button.primary { background: #4f46e5; border-color: #4f46e5; color: #fff; font-weight: 600; }
        .sheet { max-width: 210mm; margin: 16px auto 32px; background: #fff; padding: 18mm 16mm; box-shadow: 0 1px 3px rgba(0,0,0,.15); }
        .kop { text-align: center; border-bottom: 3px double #111; padding-bottom: 12px; }
        .kop h1 { font-size: 20px; margin: 0; text-transform: uppercase; letter-spacing: .5px; }
        .kop h2 { font-size: 16px; margin: 4px 0 0; }
        .kop p { font-size: 12px; margin: 4px 0 0; }
        .title { text-align: center; margin: 24px 0 16px; }
        .title h3 { margin: 0; font-size: 17px; text-transform: uppercase; text-decoration: underline; }
        .title p { margin: 4px 0 0; font-size: 14px; }
        .meta { font-size: 14px; line-height: 1.9; }
        .meta table { border-collapse: collapse; }
        .meta td { vertical-align: top; padding: 1px 6px 1px 0; }
        .body-text { font-size: 14.5px; line-height: 1.9; text-align: justify; margin-top: 12px; }
        .sign { display: flex; justify-content: flex-end; margin-top: 36px; font-size: 14px; }
        .sign div { text-align: center; min-width: 220px; }
        .sign .space { height: 80px; }
        .note { margin-top: 28px; font-size: 11.5px; color: #555; border-top: 1px dashed #999; padding-top: 8px; font-family: ui-sans-serif, system-ui, sans-serif; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .sheet { margin: 0; max-width: none; box-shadow: none; padding: 0; }
            @page { size: A4; margin: 18mm 16mm; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <a href="{{ route('letters.show', $letter) }}">Kembali</a>
        <button type="button" class="primary" onclick="window.print()">Cetak / Simpan PDF</button>
    </div>

    <div class="sheet">
        <div class="kop">
            <h1>Pengurus Rukun Tetangga / Rukun Warga</h1>
            <h2>SmartRTRW{{ $letter->user?->rt ? ' — '.$letter->user->rt->name : '' }}</h2>
            <p>Sistem Informasi RT/RW • Surat resmi diterbitkan melalui aplikasi SmartRTRW</p>
        </div>

        @php
            $judul = str_contains(strtolower($letter->letter_type), 'keterangan') ? 'SURAT KETERANGAN' : 'SURAT PENGANTAR';
        @endphp

        <div class="title">
            <h3>{{ $judul }}</h3>
            <p>Nomor: {{ $letter->letter_number }}</p>
            <p>Keperluan: {{ $letter->letter_type }}</p>
        </div>

        <div class="body-text">
            <p>Yang bertanda tangan di bawah ini, Pengurus RT/RW SmartRTRW, dengan ini menerangkan bahwa:</p>
        </div>

        <div class="meta">
            <table>
                <tr><td style="width:170px;">Nama</td><td style="width:12px;">:</td><td><strong>{{ $letter->user?->name ?? '—' }}</strong></td></tr>
                <tr><td>RT Terdaftar</td><td>:</td><td>{{ $letter->user?->rt?->name ?? '—' }}</td></tr>
                <tr><td>Keperluan</td><td>:</td><td>{{ $letter->letter_type }}</td></tr>
                <tr><td>Tanggal Pengajuan</td><td>:</td><td>{{ $letter->submission_date?->translatedFormat('d F Y') }}</td></tr>
                @if ($letter->letter_date)
                    <tr><td>Tanggal Surat</td><td>:</td><td>{{ $letter->letter_date->translatedFormat('d F Y') }}</td></tr>
                @endif
            </table>
        </div>

        <div class="body-text">
            <p>
                Adalah benar warga kami dan surat ini dipergunakan untuk keperluan
                <strong>{{ $letter->letter_type }}</strong>
                dengan keterangan sebagai berikut:
            </p>
            <p style="padding: 10px 14px; border: 1px solid #ddd; background: #fafafa;">{{ $letter->purpose }}</p>
            <p>
                Demikian surat pengantar ini dibuat untuk diurus lebih lanjut ke Kelurahan
                guna mendapatkan dokumen resmi yang dibutuhkan.
                Surat ini diterbitkan secara elektronik melalui SmartRTRW dan dapat
                diverifikasi dengan nomor surat di atas.
            </p>
        </div>

        <div class="sign">
            <div>
                <p>{{ now()->translatedFormat('d F Y') }}<br>Pengurus RT/RW</p>
                <div class="space"></div>
                <p><strong>( ............................................ )</strong></p>
            </div>
        </div>

        <div class="note">
            Dokumen ini adalah softcopy surat resmi hasil pengajuan {{ $letter->letter_number }}.
            Simpan sebagai PDF melalui tombol “Cetak / Simpan PDF”.
        </div>
    </div>
</body>
</html>
