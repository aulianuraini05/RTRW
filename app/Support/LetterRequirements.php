<?php

namespace App\Support;

/**
 * Syarat lampiran per jenis surat.
 * Tiap jenis surat butuh dokumen yang beda jumlah & jenisnya.
 */
class LetterRequirements
{
    /**
     * @return array<string, array<int, array{key: string, label: string}>>
     */
    public static function all(): array
    {
        return [
            'Surat Keterangan Domisili' => [
                ['key' => 'ktp', 'label' => 'Foto KTP'],
                ['key' => 'kk', 'label' => 'Foto Kartu Keluarga (KK)'],
            ],
            'Surat Pengantar KTP' => [
                ['key' => 'kk', 'label' => 'Foto Kartu Keluarga (KK)'],
                ['key' => 'akta_lahir', 'label' => 'Foto Akta Kelahiran'],
            ],
            'Surat Pengantar KK' => [
                ['key' => 'ktp', 'label' => 'Foto KTP'],
                ['key' => 'buku_nikah', 'label' => 'Foto Buku Nikah / Akta'],
            ],
            'Surat Keterangan Usaha' => [
                ['key' => 'ktp', 'label' => 'Foto KTP'],
                ['key' => 'kk', 'label' => 'Foto Kartu Keluarga (KK)'],
                ['key' => 'foto_usaha', 'label' => 'Foto Tempat Usaha'],
            ],
            'Surat Keterangan Tidak Mampu' => [
                ['key' => 'ktp', 'label' => 'Foto KTP'],
                ['key' => 'kk', 'label' => 'Foto Kartu Keluarga (KK)'],
                ['key' => 'foto_rumah', 'label' => 'Foto Kondisi Rumah'],
            ],
            'Surat Pengantar Nikah' => [
                ['key' => 'ktp', 'label' => 'Foto KTP'],
                ['key' => 'kk', 'label' => 'Foto Kartu Keluarga (KK)'],
                ['key' => 'akta_lahir', 'label' => 'Foto Akta Kelahiran'],
            ],
            'Surat Keterangan Kematian' => [
                ['key' => 'ktp_pelapor', 'label' => 'Foto KTP Pelapor'],
                ['key' => 'kk', 'label' => 'Foto Kartu Keluarga (KK)'],
                ['key' => 'surat_rs', 'label' => 'Surat Keterangan RS / Faskes'],
            ],
            'Lainnya' => [
                ['key' => 'dokumen', 'label' => 'Dokumen Pendukung'],
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public static function types(): array
    {
        return array_keys(static::all());
    }

    /**
     * @return array<int, array{key: string, label: string}>
     */
    public static function for(string $type): array
    {
        return static::all()[$type] ?? [];
    }
}
