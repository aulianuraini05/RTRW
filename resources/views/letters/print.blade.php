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
        .sign { display: flex; justify-content: flex-end; margin-top: 32px; font-size: 14px; }
        .sign .sign-container { text-align: center; min-width: 240px; position: relative; }
        .sign .signature-box { position: relative; width: 240px; height: 100px; margin: 4px auto; display: flex; align-items: center; justify-content: center; }
        .sign .signature-img { height: 80px; width: auto; z-index: 2; position: relative; filter: drop-shadow(0px 1px 1px rgba(0,0,0,0.15)); }
        .sign .stamp-img { position: absolute; left: 20px; top: 0px; width: 100px; height: 100px; opacity: 0.88; z-index: 1; pointer-events: none; transform: rotate(-6deg); }
        .verified-badge { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; color: #1e40af; background: #eff6ff; border: 1px solid #bfdbfe; padding: 4px 10px; border-radius: 4px; margin-top: 6px; }
        .note { margin-top: 24px; font-size: 11.5px; color: #555; border-top: 1px dashed #999; padding-top: 10px; font-family: ui-sans-serif, system-ui, sans-serif; }
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
        <button type="button" class="primary" onclick="window.print()">Simpan PDF</button>
    </div>

    <div class="sheet">
        @php $rwGlobal = \App\Models\Rw::first(); @endphp
        <div class="kop">
            <h1>Pengurus Rukun Tetangga / Rukun Warga</h1>
            <h2>SmartRTRW{{ $letter->user?->rt ? ' — '.$letter->user->rt->name : ' — RT 01' }} / {{ $rwGlobal?->name ?? 'RW 10' }}</h2>
            <p>
                {{ $rwGlobal?->alamat_lengkap ? $rwGlobal->alamat_lengkap.' • ' : '' }}
                Kel. {{ $rwGlobal?->kelurahan ?? '-' }}, Kec. {{ $rwGlobal?->kecamatan ?? '-' }}, {{ $rwGlobal?->kota_kabupaten ?? '-' }}{{ $rwGlobal?->kode_pos ? ' '.$rwGlobal->kode_pos : '' }}
                • Sistem Informasi RT/RW
            </p>
        </div>

        @php
            $judul = str_contains(strtolower($letter->letter_type), 'keterangan') ? 'SURAT KETERANGAN' : 'SURAT PENGANTAR';
            $rtName = $letter->user?->rt?->name ?? 'RT 01';
            $ketuaName = 'H. Ahmad Subagja, S.T.';
            $ttdPath = asset('images/signatures/ttd-ketua-rt01.svg');
            $stempelPath = asset('images/stamps/stempel-rt01.svg');
        @endphp

        <div class="title">
            <h3>{{ $judul }}</h3>
            <p>Nomor: {{ $letter->letter_number }}</p>
            <p>Keperluan: {{ $letter->letter_type }}</p>
        </div>

        <div class="body-text">
            <p>Yang bertanda tangan di bawah ini, Pengurus {{ $rtName }} {{ $rwGlobal?->name ?? 'RW 10' }} SmartRTRW, dengan ini menerangkan bahwa:</p>
        </div>

        <div class="meta">
            <table>
                <tr><td style="width:170px;">Nama Warga</td><td style="width:12px;">:</td><td><strong>{{ $letter->user?->name ?? '—' }}</strong></td></tr>
                <tr><td>NIK</td><td>:</td><td>{{ $letter->user?->nik ?? '—' }}</td></tr>
                <tr><td>No. KK</td><td>:</td><td>{{ $letter->user?->no_kk ?? '—' }}</td></tr>
                <tr><td>TTL</td><td>:</td><td>{{ $letter->user?->tempat_lahir ? $letter->user->tempat_lahir.', '.($letter->user->tanggal_lahir?->translatedFormat('d F Y') ?? '-') : '—' }}</td></tr>
                <tr><td>Alamat</td><td>:</td><td>{{ $letter->user?->alamat_rumah ? $letter->user->alamat_rumah.($letter->user->no_rumah ? ' No. '.$letter->user->no_rumah : '').', '.$rtName.' / '.($rwGlobal?->name ?? 'RW 10').', Kel. '.($rwGlobal?->kelurahan ?? '-').', Kec. '.($rwGlobal?->kecamatan ?? '-') : '—' }}</td></tr>
                <tr><td>RT / Wilayah</td><td>:</td><td>{{ $rtName }} / {{ $rwGlobal?->name ?? 'RW 10' }}</td></tr>
                <tr><td>Jenis Surat</td><td>:</td><td>{{ $letter->letter_type }}</td></tr>
                <tr><td>Tanggal Pengajuan</td><td>:</td><td>{{ $letter->submission_date?->translatedFormat('d F Y') }}</td></tr>
                @if ($letter->letter_date)
                    <tr><td>Tanggal Disetujui</td><td>:</td><td>{{ $letter->letter_date->translatedFormat('d F Y') }}</td></tr>
                @endif
            </table>
        </div>

        <div class="body-text">
            <p>
                Adalah benar-benar warga yang terdaftar di lingkungan kami dan surat ini dipergunakan untuk keperluan
                <strong>{{ $letter->letter_type }}</strong>
                dengan rincian/keterangan sebagai berikut:
            </p>
            <p style="padding: 10px 14px; border: 1px solid #d1d5db; background: #f9fafb; border-radius: 4px; font-weight: 500;">{{ $letter->purpose }}</p>
            <p>
                Demikian surat pengantar ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.
                Surat ini diterbitkan secara elektronik melalui sistem SmartRTRW dan telah dilengkapi tanda tangan serta stempel digital resmi.
            </p>
        </div>

        <div class="sign">
            <div class="sign-container">
                <p style="margin:0 0 4px 0;">{{ ($letter->letter_date ?? now())->translatedFormat('d F Y') }}<br>Ketua {{ $rtName }} {{ $rwGlobal?->name ?? 'RW 10' }}</p>
                <div class="signature-box">
                    <img src="{{ $stempelPath }}" alt="Stempel Resmi RT 01" class="stamp-img">
                    <img src="{{ $ttdPath }}" alt="Tanda Tangan Ketua RT 01" class="signature-img">
                </div>
                <p style="margin: 0; font-weight: bold; text-decoration: underline; font-size: 15px;">{{ $ketuaName }}</p>
                <p style="margin: 2px 0 0; font-size: 12px; color: #374151;">Ketua {{ $rtName }}</p>
            </div>
        </div>

        <div class="note">
            <div class="verified-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <strong>TERVERIFIKASI DIGITAL:</strong> Softcopy surat resmi ini telah ditandatangani dan distempel secara sah oleh Ketua {{ $rtName }}.
            </div>
            <p style="margin: 6px 0 0; color: #6b7280; font-size: 11px;">Nomor Dokumen: {{ $letter->letter_number }} • Dapat disimpan langsung dalam format PDF.</p>
        </div>
    </div>
</body>
</html>
