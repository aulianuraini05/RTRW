# Tasks - Smart RT/RW Information System

Dokumen ini digunakan untuk melacak progres pengembangan sistem berdasarkan PRD. Tujuannya adalah melihat dengan jelas apa yang sudah selesai, apa yang masih belum dikerjakan, dan apa yang akan dikerjakan berikutnya.

## 1. Status Umum Proyek
- Status keseluruhan: Seluruh modul inti selesai dan terintegrasi end-to-end.
- Fokus prioritas: polish sisa (search/filter, riwayat aktivitas, dashboard ringkasan), lalu deploy dan uji coba.

## 2. Keterangan Status
- [x] Sudah selesai
- [ ] Belum dimulai / belum selesai
- [~] Sedang dikerjakan / butuh review

## 3. Ringkasan Pekerjaan

### A. Dokumen & Perencanaan
- [x] Menyusun PRD awal untuk sistem Smart RT/RW
- [x] Menyusun daftar tasks pelacakan progres proyek
- [ ] Menyepakati prioritas implementasi fase 1, 2, dan 3
- [x] Menentukan skema role pengguna (admin/RT/RW vs warga)

### B. Fondasi Sistem
- [x] Project Laravel sudah tersedia
- [x] Auth default Laravel tersedia
- [x] Menghubungkan halaman dashboard dengan navigasi modul utama
- [x] Menambahkan middleware dan otorisasi per role
- [x] Menyiapkan layout umum yang konsisten untuk admin dan warga

### C. Modul Pengumuman (Announcement)
- [x] Controller untuk pengumuman tersedia
- [x] Model Announcement tersedia
- [x] View pengumuman (list, detail, form) tersedia
- [x] Route pengumuman terhubung ke aplikasi
- [x] Form create/edit/delete selesai dan bisa dipakai
- [x] Validasi input dan status pengumuman selesai
- [x] Tampilan list/detail pengumuman untuk warga selesai

### D. Modul Aspirasi / Pengaduan (Aspiration)
- [x] Controller untuk aspirasi tersedia
- [x] Model Aspiration tersedia
- [x] Folder view aspirasi tersedia
- [x] Route aspirasi terhubung ke aplikasi
- [x] Relasi aspirasi dengan akun warga dan pembatasan data per pemilik selesai
- [x] Form pengajuan aspirasi warga selesai
- [x] Navigasi Aspirasi warga mengarah ke form pengajuan
- [x] Workflow status (diterima/diproses/ditolak) selesai
- [x] Panel RT/RW untuk memproses aspirasi selesai
- [x] Aspirasi baru menampilkan ACC/Tolak, lalu Ubah Status setelah keputusan RT/RW diberikan
- [x] Upload foto pendukung aspirasi oleh warga selesai (JPG/PNG/WEBP, maks 10 MB)
- [x] Tanggapan pengurus RT/RW/Admin untuk aspirasi selesai

### E. Modul Aset (Asset)
- [x] Controller untuk aset tersedia
- [x] Model Asset tersedia
- [x] Folder view aset tersedia
- [x] Route aset terhubung ke aplikasi
- [x] CRUD aset oleh RT/RW selesai
- [x] Form peminjaman aset oleh warga selesai
- [x] Workflow persetujuan peminjaman selesai

### F. Modul Kas (Cash Transaction)
- [x] Controller kas tersedia
- [x] Model CashTransaction tersedia
- [x] Folder view kas tersedia
- [x] Route kas terhubung ke aplikasi
- [x] Pencatatan transaksi kas oleh RT/RW selesai
- [x] Status pembayaran kas per warga selesai
- [x] Form pengajuan pembayaran kas oleh warga selesai
- [x] Integrasi pembayaran online selesai (simulasi sandbox: VA/QRIS/Transfer, generate kode pembayaran, bayar online)

### G. Modul Iuran Warga (Contribution)
- [x] Controller kontribusi tersedia
- [x] Model Contribution tersedia
- [x] Folder view kontribusi tersedia
- [x] Route iuran terhubung ke aplikasi
- [x] Pencatatan iuran oleh RT/RW selesai
- [x] Status pembayaran iuran per warga selesai
- [x] Form pengajuan pembayaran iuran oleh warga selesai
- [x] Integrasi pembayaran online selesai (simulasi sandbox: VA/QRIS/Transfer, generate kode pembayaran, bayar online)

### H. Modul Persuratan (Letter)
- [x] Controller surat tersedia
- [x] Model Letter tersedia
- [x] Folder view surat tersedia
- [x] Route persuratan terhubung ke aplikasi
- [x] Form pengajuan surat warga selesai
- [x] Approval/reject oleh RT/RW selesai
- [x] Riwayat status surat selesai
- [x] Soft doc surat resmi auto-generate (cetak/simpan PDF) hanya untuk status disetujui/selesai
- [x] Lampiran syarat dinamis per jenis surat (Domisili 2, Nikah 3, dst. — tiap syarat 1 file JPG/PNG/WEBP/PDF maks 10 MB)

### I. Modul Marketplace / UMKM
- [x] Controller marketplace tersedia
- [x] Model Marketplace tersedia
- [x] Folder view marketplace tersedia
- [x] Route marketplace terhubung ke aplikasi
- [x] Form daftar produk UMKM selesai
- [x] Tampilan katalog produk selesai
- [x] Tombol "Beli via WhatsApp" langsung membuka chat WA penjual dengan template pesan selesai
- [x] Stok otomatis dan riwayat pembelian/penjualan dihapus (transaksi lewat chat WA penjual)

## 4. Pekerjaan Cross-Cutting / Tambahan
- [x] Pencarian, filter, dan sorting data untuk tiap modul
- [x] Notifikasi status perubahan untuk pengguna
- [x] Upload dokumen pendukung untuk pengaduan dan persuratan
- [ ] Riwayat aktivitas pengguna dan transaksi
- [x] Testing fitur utama
- [ ] Deploy awal dan uji coba pengguna

## 5. Prioritas Implementasi Selanjutnya
1. Standardisasi pencarian, filter, dan sorting (Aspirasi, Aset, Surat).
2. Riwayat aktivitas pengguna dan transaksi.
3. Dashboard ringkasan + export laporan.
4. Deploy awal dan uji coba pengguna.

## 6. Catatan Saat Ini
- Struktur modul inti sudah mulai tersedia di dalam project.
- Modul kas dan iuran sudah mengikuti desain per-warga sesuai PRD: warga dapat mengajukan pembayaran (status pending) dan RT/RW memverifikasi menjadi lunas/ditolak.
- Marketplace (UMKM) sudah lengkap: warga dapat mendaftarkan produk, melihat katalog. Tombol "Beli via WhatsApp" membuka chat WA penjual secara langsung dengan template pesan otomatis berisi nama produk dan harga. Stok otomatis dan sistem riwayat pembelian/penjualan (MarketplacePurchase) sudah dihapus karena transaksi dialihkan ke WhatsApp penjual. Sudah ada test khusus (MarketplaceTest).
- Integrasi pembayaran online (fase 3) sudah dikerjakan dalam bentuk simulasi/sandbox untuk modul kas dan iuran: warga mengisi jumlah & memilih metode (Virtual Account/QRIS/Transfer), sistem generate kode pembayaran, lalu warga menyelesaikan lewat tombol "Bayar Sekarang (Simulasi)" yang mengubah status menjadi lunas + mencatat paid_at. Sudah ada test tambahan (KasTest & IuranTest).
- Notifikasi status perubahan sudah dikerjakan dan di-commit (32f242c): ada tabel notifications, service Notifier, lonceng di navigasi/sidebar, dan hook notifikasi di pengumuman, aspirasi, aset/peminjaman, kas, iuran, dan persuratan.
- Aspirasi sudah mendukung upload foto pendukung dan tanggapan pengurus (kolom tanggapan/tanggapan_by/tanggapan_at/photo_path), plus validasi foto wajib untuk aset Ketua RT. Timezone diubah ke Asia/Jakarta.
- Persuratan sekarang pakai lampiran dinamis per jenis surat (LetterRequirements + tabel letter_attachments, tiap syarat 1 file, tampil di detail + edit read-only, hapus otomatis saat surat dihapus).
- Testing fitur utama sudah dijalankan (131 passed) dan tidak ada masalah.
- Aspirasi: RT mencatat rt_id saat pengajuan (migrasi add_rt_id_to_aspirations) agar aspirasi tidak hilang dari daftar RT saat akun warga dihapus. Tombol "Teruskan ke RW" dihapus dari UI karena RW sudah melihat semua aspirasi langsung.
- Filter/search/sorting sudah lengkap semua modul: Aspirasi (cari judul/isi + status + terbaru/terlama), Aset (cari nama/jenis/deskripsi + kondisi + nama A-Z), Surat (cari nomor/jenis/keperluan + status + jenis + terbaru/terlama). Test LetterPrivacyTest diperbaiki pakai nomor surat karena dropdown jenis ikut menampilkan semua nama.
- Dokumen ini bisa terus diperbarui setiap kali ada progress baru.
