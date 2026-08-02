# Tasks - Smart RT/RW Information System

Dokumen ini digunakan untuk melacak progres pengembangan sistem berdasarkan PRD. Tujuannya adalah melihat dengan jelas apa yang sudah selesai, apa yang masih belum dikerjakan, dan apa yang akan dikerjakan berikutnya.

## 1. Status Umum Proyek
- Status keseluruhan: Persiapan dan struktur modul awal sudah ada, tetapi integrasi fitur end-to-end masih perlu diselesaikan.
- Fokus prioritas: menyatukan modul inti, menghubungkan route, memperbaiki akses per role, dan memastikan alur CRUD berjalan.

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
- [ ] Form pengajuan aspirasi warga selesai
- [ ] Workflow status (diterima/diproses/ditolak) selesai
- [ ] Panel RT/RW untuk memproses aspirasi selesai

### E. Modul Aset (Asset)
- [x] Controller untuk aset tersedia
- [x] Model Asset tersedia
- [x] Folder view aset tersedia
- [x] Route aset terhubung ke aplikasi
- [ ] CRUD aset oleh RT/RW selesai
- [ ] Form peminjaman aset oleh warga selesai
- [ ] Workflow persetujuan peminjaman selesai

### F. Modul Kas (Cash Transaction)
- [x] Controller kas tersedia
- [x] Model CashTransaction tersedia
- [x] Folder view kas tersedia
- [x] Route kas terhubung ke aplikasi
- [ ] Pencatatan transaksi kas oleh RT/RW selesai
- [ ] Status pembayaran kas per warga selesai
- [ ] Integrasi pembayaran online selesai

### G. Modul Iuran Warga (Contribution)
- [x] Controller kontribusi tersedia
- [x] Model Contribution tersedia
- [x] Folder view kontribusi tersedia
- [x] Route iuran terhubung ke aplikasi
- [ ] Pencatatan iuran oleh RT/RW selesai
- [ ] Status pembayaran iuran per warga selesai
- [ ] Integrasi pembayaran online selesai

### H. Modul Persuratan (Letter)
- [x] Controller surat tersedia
- [x] Model Letter tersedia
- [x] Folder view surat tersedia
- [x] Route persuratan terhubung ke aplikasi
- [ ] Form pengajuan surat warga selesai
- [ ] Approval/reject oleh RT/RW selesai
- [ ] Riwayat status surat selesai

### I. Modul Marketplace / UMKM
- [x] Controller marketplace tersedia
- [x] Model Marketplace tersedia
- [x] Folder view marketplace tersedia
- [x] Route marketplace terhubung ke aplikasi
- [ ] Form daftar produk UMKM selesai
- [ ] Tampilan katalog produk selesai
- [ ] Proses pembelian sederhana selesai

## 4. Pekerjaan Cross-Cutting / Tambahan
- [ ] Pencarian, filter, dan sorting data untuk tiap modul
- [ ] Notifikasi status perubahan untuk pengguna
- [ ] Upload dokumen pendukung untuk pengaduan dan persuratan
- [ ] Riwayat aktivitas pengguna dan transaksi
- [ ] Testing fitur utama
- [ ] Deploy awal dan uji coba pengguna

## 5. Prioritas Implementasi Selanjutnya
1. Hubungkan route dan controller ke view yang sudah ada.
2. Selesaikan otorisasi per role admin dan warga.
3. Fokus pada modul inti: pengumuman, aspirasi, persuratan.
4. Lanjutkan modul administrasi: aset, kas, iuran.
5. Tambahkan marketplace dan integrasi pembayaran online.

## 6. Catatan Saat Ini
- Struktur modul inti sudah mulai tersedia di dalam project.
- Masih diperlukan pengerjaan integrasi antar komponen agar aplikasi benar-benar bisa dipakai sebagai sistem end-to-end.
- Dokumen ini bisa terus diperbarui setiap kali ada progress baru.
