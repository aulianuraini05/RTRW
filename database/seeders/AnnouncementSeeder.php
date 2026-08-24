<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $announcements = [
            [
                'announcement_title' => 'Jadwal & Panduan Sholat Tarawih Serta Penyerahan Zakat Fitrah Bulan Puasa Ramadhan 1447H',
                'announcement_content' => 'Pengurus RT/RW mengundang seluruh warga muslim untuk meramaikan Sholat Tarawih berjamaah di Masjid Al-Ikhlas. Panitia juga telah membuka penerimaan Zakat Fitrah dan Sedekah Ramadhan mulai hari ini.',
                'publication_date' => '2026-08-19',
                'category' => 'kegiatan',
                'priority' => 'penting',
                'is_pinned' => true,
                'status' => 'active',
                'read_count' => 142,
            ],
            [
                'announcement_title' => 'Kerja Bakti Massal & Gerakan Memilah Sampah Daur Ulang Lingkungan RT/RW',
                'announcement_content' => 'Dalam rangka menjaga kebersihan dan kesehatan lingkungan taman kota, mari ikuti kegiatan gotong royong dan penghijauan pada hari Minggu pukul 07.00 WIB.',
                'publication_date' => '2026-08-20',
                'category' => 'lingkungan',
                'priority' => 'biasa',
                'is_pinned' => false,
                'status' => 'active',
                'read_count' => 98,
            ],
            [
                'announcement_title' => 'Peningkatan Keamanan Siskamling & Pemasangan Kamera CCTV Baru di Setiap Pos Ronda',
                'announcement_content' => 'Guna mengantisipasi gangguan keamanan malam hari, pengurus telah memperbarui gembok gerbang utama dan memasang CCTV online 24 jam. Dihimbau seluruh warga memperketat kunci rumah.',
                'publication_date' => '2026-08-21',
                'category' => 'keamanan',
                'priority' => 'mendesak',
                'is_pinned' => true,
                'status' => 'active',
                'read_count' => 256,
            ],
            [
                'announcement_title' => 'Pendaftaran Lomba Semarak 17 Agustus Kemerdekaan RI Ke-81 Segera Dibuka!',
                'announcement_content' => 'Sambut hari kemerdekaan Indonesia dengan meriah! Daftarkan putra-putri Anda dalam lomba balap karung, makan kerupuk, mewarnai, serta pawai bendera Merah Putih tingkat RT/RW.',
                'publication_date' => '2026-08-22',
                'category' => 'kegiatan',
                'priority' => 'penting',
                'is_pinned' => false,
                'status' => 'active',
                'read_count' => 180,
            ],
            [
                'announcement_title' => 'Baca Yaa! Informasi Penting Perubahan Jadwal Pengangkutan Sampah & Iuran Warga Online',
                'announcement_content' => 'Dimohon perhatian seluruh warga mengenai penyesuaian jam kerja petugas kebersihan lingkungan serta petunjuk teknis pembayaran iuran kas RT/RW via transfer bank.',
                'publication_date' => '2026-08-24',
                'category' => 'umum',
                'priority' => 'mendesak',
                'is_pinned' => false,
                'status' => 'active',
                'read_count' => 310,
            ],
        ];

        foreach ($announcements as $data) {
            Announcement::updateOrCreate(
                ['announcement_title' => $data['announcement_title']],
                $data
            );
        }
    }
}
