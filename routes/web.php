<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AspirationController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetLoanController;
use App\Http\Controllers\CashTransactionController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\IuranScheduleController;
use App\Http\Controllers\KasScheduleController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WargaController;
use App\Models\Asset;
use App\Models\CashTransaction;
use App\Models\Contribution;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // ── Data ringkas untuk section #admin di single-page landing ──
    // (logika sama persis dengan route /administrasi agar tampilan konsisten)
    $totalKas = CashTransaction::where('payment_status', 'lunas')->sum('amount');

    $totalKasAkhirBulanLalu = CashTransaction::where('payment_status', 'lunas')
        ->where('paid_at', '<', Carbon::now()->startOfMonth())
        ->sum('amount');

    $persentaseKas = $totalKasAkhirBulanLalu > 0
        ? round((($totalKas - $totalKasAkhirBulanLalu) / $totalKasAkhirBulanLalu) * 100, 1)
        : ($totalKas > 0 ? 100.0 : 0.0);

    $riwayatKas = collect();
    for ($i = 5; $i >= 0; $i--) {
        $bulan = Carbon::now()->subMonths($i);
        $riwayatKas->push([
            'label' => $bulan->format('M'),
            'total' => CashTransaction::where('payment_status', 'lunas')
                ->where('paid_at', '<', $bulan->copy()->addMonth()->startOfMonth())
                ->sum('amount'),
        ]);
    }

    $totalKK = User::where('role', 'warga')->count();
    $kkSudahBayar = Contribution::where('payment_status', 'lunas')
        ->whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)
        ->distinct('user_id')
        ->count('user_id');
    $kkBelumBayar = max(0, $totalKK - $kkSudahBayar);
    $persentaseIuran = $totalKK > 0 ? round(($kkSudahBayar / $totalKK) * 100, 1) : 0;

    $totalAset = Asset::sum('quantity');
    $asetBaik = Asset::where('condition', 'baik')->sum('quantity');
    $asetRusakRingan = Asset::where('condition', 'rusak ringan')->sum('quantity');
    $asetRusakBerat = Asset::where('condition', 'perlu perbaikan')->sum('quantity');

    return view('welcome', compact(
        'totalKas', 'persentaseKas', 'riwayatKas',
        'persentaseIuran', 'kkSudahBayar', 'kkBelumBayar',
        'totalAset', 'asetBaik', 'asetRusakRingan', 'asetRusakBerat'
    ));
});

Route::get('/administrasi', function () {
    // ── Kas RW ──────────────────────────────────────────────
    $totalKas = CashTransaction::where('payment_status', 'lunas')->sum('amount');

    $totalKasAkhirBulanLalu = CashTransaction::where('payment_status', 'lunas')
        ->where('paid_at', '<', Carbon::now()->startOfMonth())
        ->sum('amount');

    $persentaseKas = $totalKasAkhirBulanLalu > 0
        ? round((($totalKas - $totalKasAkhirBulanLalu) / $totalKasAkhirBulanLalu) * 100, 1)
        : ($totalKas > 0 ? 100.0 : 0.0);

    $riwayatKas = collect();
    for ($i = 5; $i >= 0; $i--) {
        $bulan = Carbon::now()->subMonths($i);
        $riwayatKas->push([
            'label' => $bulan->format('M'),
            'total' => CashTransaction::where('payment_status', 'lunas')
                ->where('paid_at', '<', $bulan->copy()->addMonth()->startOfMonth())
                ->sum('amount'),
        ]);
    }

    // ── Iuran Warga ─────────────────────────────────────────
    $totalKK = User::where('role', 'warga')->count();
    $kkSudahBayar = Contribution::where('payment_status', 'lunas')
        ->whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)
        ->distinct('user_id')
        ->count('user_id');
    $kkBelumBayar = max(0, $totalKK - $kkSudahBayar);
    $persentaseIuran = $totalKK > 0 ? round(($kkSudahBayar / $totalKK) * 100, 1) : 0;

    // ── Aset Lingkungan ─────────────────────────────────────
    $totalAset = Asset::sum('quantity');
    $asetBaik = Asset::where('condition', 'baik')->sum('quantity');
    $asetRusakRingan = Asset::where('condition', 'rusak ringan')->sum('quantity');
    $asetRusakBerat = Asset::where('condition', 'perlu perbaikan')->sum('quantity');

    return view('administrasi', compact(
        'totalKas', 'persentaseKas', 'riwayatKas',
        'persentaseIuran', 'kkSudahBayar', 'kkBelumBayar',
        'totalAset', 'asetBaik', 'asetRusakRingan', 'asetRusakBerat'
    ));
})->name('administrasi');

Route::get('/layanan', function () {
    return view('layanan');
})->name('layanan');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Module Routes (Authenticated)
|--------------------------------------------------------------------------
|
| Semua route modul utama Smart RT/RW.
| - Route /create HARUS didefinisikan SEBELUM /{id} agar tidak konflik.
| - Route khusus admin dilindungi middleware 'role:admin'.
| - Route yang bisa diakses warga terbuka untuk semua user login.
|
*/

Route::middleware('auth')->group(function () {

    // =========================================================================
    // PENGUMUMAN (Announcement)
    // =========================================================================
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');

    // Admin only: kelola pengumuman
    Route::middleware('role:admin')->group(function () {
        Route::get('/announcements/create', [AnnouncementController::class, 'create'])->name('announcements.create');
        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::get('/announcements/{announcement}/readers', [AnnouncementController::class, 'readers'])->name('announcements.readers');
        Route::get('/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
        Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
        Route::patch('/announcements/{announcement}/toggle', [AnnouncementController::class, 'toggleStatus'])->name('announcements.toggle');
        Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    });

    // Warga & Admin: lihat detail (SETELAH /create agar tidak konflik)
    Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show'])->name('announcements.show');

    // =========================================================================
    // ASPIRASI / PENGADUAN (Aspiration)
    // =========================================================================
    Route::get('/aspirations', [AspirationController::class, 'index'])->name('aspirations.index');

    // Warga: mengajukan aspirasi baru
    Route::middleware('role:warga')->group(function () {
        Route::get('/aspirations/create', [AspirationController::class, 'create'])->name('aspirations.create');
        Route::post('/aspirations', [AspirationController::class, 'store'])->name('aspirations.store');
    });

    // Admin only: edit & hapus aspirasi (proses/approve/reject)
    Route::middleware('role:admin')->group(function () {
        Route::patch('/aspirations/{aspiration}/status', [AspirationController::class, 'updateStatus'])->name('aspirations.status.update');
        Route::get('/aspirations/{aspiration}/edit', [AspirationController::class, 'edit'])->name('aspirations.edit');
        Route::put('/aspirations/{aspiration}', [AspirationController::class, 'update'])->name('aspirations.update');
        Route::delete('/aspirations/{aspiration}', [AspirationController::class, 'destroy'])->name('aspirations.destroy');
    });

    // Warga & Admin: lihat detail
    Route::get('/aspirations/{aspiration}', [AspirationController::class, 'show'])->name('aspirations.show');

    // =========================================================================
    // PERSURATAN (Letter)
    // =========================================================================
    Route::get('/letters', [LetterController::class, 'index'])->name('letters.index');

    // Warga: mengajukan surat baru
    Route::middleware('role:warga')->group(function () {
        Route::get('/letters/create', [LetterController::class, 'create'])->name('letters.create');
        Route::post('/letters', [LetterController::class, 'store'])->name('letters.store');
    });

    // Admin only: kelola surat (approve/reject/proses/hapus)
    Route::middleware('role:admin')->group(function () {
        Route::patch('/letters/{letter}/status', [LetterController::class, 'updateStatus'])->name('letters.status.update');
        Route::get('/letters/{letter}/edit', [LetterController::class, 'edit'])->name('letters.edit');
        Route::put('/letters/{letter}', [LetterController::class, 'update'])->name('letters.update');
        Route::delete('/letters/{letter}', [LetterController::class, 'destroy'])->name('letters.destroy');
    });

    // Warga & Admin: lihat detail + cetak surat resmi (PDF via print)
    Route::get('/letters/{letter}/cetak', [LetterController::class, 'cetak'])->name('letters.cetak');
    Route::get('/letters/{letter}', [LetterController::class, 'show'])->name('letters.show');

    // =========================================================================
    // ASET (Asset)
    // =========================================================================
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');

    // Admin only: kelola aset (CRUD)
    Route::middleware('role:admin')->group(function () {
        Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
        Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
        Route::get('/assets/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');
        Route::put('/assets/{asset}', [AssetController::class, 'update'])->name('assets.update');
        Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');
    });

    // Warga: ajukan peminjaman aset
    Route::middleware('role:warga')->group(function () {
        Route::post('/assets/{asset}/loans', [AssetLoanController::class, 'store'])->name('asset-loans.store');
    });

    // Admin only: proses peminjaman
    Route::middleware('role:admin')->group(function () {
        Route::patch('/asset-loans/{loan}/status', [AssetLoanController::class, 'updateStatus'])->name('asset-loans.status.update');
    });

    // Warga & Admin: lihat detail
    Route::get('/assets/{asset}', [AssetController::class, 'show'])->name('assets.show');

    // =========================================================================
    // KAS (Cash Transaction)
    // =========================================================================
    Route::get('/cash-transactions', [CashTransactionController::class, 'index'])->name('cash_transactions.index');

    // Admin & Warga: catat / ajukan pembayaran kas
    Route::get('/cash-transactions/create', [CashTransactionController::class, 'create'])->name('cash_transactions.create');
    Route::post('/cash-transactions', [CashTransactionController::class, 'store'])->name('cash_transactions.store');

    // Admin only: kelola & verifikasi transaksi kas
    Route::middleware('role:admin')->group(function () {
        Route::patch('/cash-transactions/{cashTransaction}/status', [CashTransactionController::class, 'updateStatus'])->name('cash_transactions.status.update');
        Route::get('/cash-transactions/{cashTransaction}/edit', [CashTransactionController::class, 'edit'])->name('cash_transactions.edit');
        Route::put('/cash-transactions/{cashTransaction}', [CashTransactionController::class, 'update'])->name('cash_transactions.update');
        Route::delete('/cash-transactions/{cashTransaction}', [CashTransactionController::class, 'destroy'])->name('cash_transactions.destroy');
    });

    // Warga: selesaikan pembayaran kas online (simulasi)
    Route::post('/cash-transactions/{cashTransaction}/pay', [CashTransactionController::class, 'payOnline'])->name('cash_transactions.pay');

    // Warga & Admin: lihat detail
    Route::get('/cash-transactions/{cashTransaction}', [CashTransactionController::class, 'show'])->name('cash_transactions.show');

    // =========================================================================
    // JADWAL KAS BULANAN PER RT (KasSchedule)
    // =========================================================================
    // Ketua RT mengisi nominal kas bulan berjalan untuk RT-nya;
    // sistem otomatis menerbitkan tagihan ke warga RT tersebut.
    Route::middleware('role:admin')->group(function () {
        Route::get('/kas-schedules', [KasScheduleController::class, 'index'])->name('kas_schedules.index');
        Route::get('/kas-schedules/create', [KasScheduleController::class, 'create'])->name('kas_schedules.create');
        Route::post('/kas-schedules', [KasScheduleController::class, 'store'])->name('kas_schedules.store');
        Route::post('/kas-schedules/{kasSchedule}/sync', [KasScheduleController::class, 'sync'])->name('kas_schedules.sync');
        Route::delete('/kas-schedules/{kasSchedule}', [KasScheduleController::class, 'destroy'])->name('kas_schedules.destroy');
        Route::get('/kas-schedules/{kasSchedule}', [KasScheduleController::class, 'show'])->name('kas_schedules.show');
    });

    // =========================================================================
    // IURAN WARGA (Contribution)
    // =========================================================================
    Route::get('/contributions', [ContributionController::class, 'index'])->name('contributions.index');

    // Admin & Warga: catat / ajukan pembayaran iuran
    Route::get('/contributions/create', [ContributionController::class, 'create'])->name('contributions.create');
    Route::post('/contributions', [ContributionController::class, 'store'])->name('contributions.store');

    // Admin only: kelola & verifikasi iuran
    Route::middleware('role:admin')->group(function () {
        Route::patch('/contributions/{contribution}/status', [ContributionController::class, 'updateStatus'])->name('contributions.status.update');
        Route::get('/contributions/{contribution}/edit', [ContributionController::class, 'edit'])->name('contributions.edit');
        Route::put('/contributions/{contribution}', [ContributionController::class, 'update'])->name('contributions.update');
        Route::delete('/contributions/{contribution}', [ContributionController::class, 'destroy'])->name('contributions.destroy');
    });

    // Warga: selesaikan pembayaran iuran online (simulasi)
    Route::post('/contributions/{contribution}/pay', [ContributionController::class, 'payOnline'])->name('contributions.pay');

    // Warga & Admin: lihat detail
    Route::get('/contributions/{contribution}', [ContributionController::class, 'show'])->name('contributions.show');

    // =========================================================================
    // JADWAL IURAN BULANAN PER RT (IuranSchedule)
    // =========================================================================
    // Ketua RT mengisi nominal iuran bulan berjalan untuk RT-nya;
    // sistem otomatis menerbitkan tagihan ke warga RT tersebut.
    Route::middleware('role:admin')->group(function () {
        Route::get('/iuran-schedules', [IuranScheduleController::class, 'index'])->name('iuran_schedules.index');
        Route::get('/iuran-schedules/create', [IuranScheduleController::class, 'create'])->name('iuran_schedules.create');
        Route::post('/iuran-schedules', [IuranScheduleController::class, 'store'])->name('iuran_schedules.store');
        Route::post('/iuran-schedules/{iuranSchedule}/sync', [IuranScheduleController::class, 'sync'])->name('iuran_schedules.sync');
        Route::delete('/iuran-schedules/{iuranSchedule}', [IuranScheduleController::class, 'destroy'])->name('iuran_schedules.destroy');
        Route::get('/iuran-schedules/{iuranSchedule}', [IuranScheduleController::class, 'show'])->name('iuran_schedules.show');
    });

    // =========================================================================
    // MARKETPLACE / UMKM (Marketplace)
    // =========================================================================
    Route::get('/marketplaces', [MarketplaceController::class, 'index'])->name('marketplaces.index');

    // Warga & Admin: mendaftarkan produk baru
    Route::get('/marketplaces/create', [MarketplaceController::class, 'create'])->name('marketplaces.create');
    Route::post('/marketplaces', [MarketplaceController::class, 'store'])->name('marketplaces.store');

    // Pemilik / Admin: edit produk
    Route::get('/marketplaces/{marketplace}/edit', [MarketplaceController::class, 'edit'])->name('marketplaces.edit');
    Route::put('/marketplaces/{marketplace}', [MarketplaceController::class, 'update'])->name('marketplaces.update');

    // Admin only: hapus produk
    Route::middleware('role:admin')->group(function () {
        Route::delete('/marketplaces/{marketplace}', [MarketplaceController::class, 'destroy'])->name('marketplaces.destroy');
    });

    // Warga & Admin: lihat detail
    Route::get('/marketplaces/{marketplace}', [MarketplaceController::class, 'show'])->name('marketplaces.show');

    // =========================================================================
    // DATA WARGA (per RT - Ketua RT hanya lihat & CRUD RT sendiri, RW lihat semua)
    // =========================================================================
    Route::middleware('role:admin')->group(function () {
        Route::get('/warga', [WargaController::class, 'index'])->name('warga.index');
        Route::get('/warga/create', [WargaController::class, 'create'])->name('warga.create');
        Route::post('/warga', [WargaController::class, 'store'])->name('warga.store');
        Route::get('/warga/{user}/edit', [WargaController::class, 'edit'])->name('warga.edit');
        Route::put('/warga/{user}', [WargaController::class, 'update'])->name('warga.update');
        Route::delete('/warga/{user}', [WargaController::class, 'destroy'])->name('warga.destroy');
        Route::get('/warga/{user}', [WargaController::class, 'show'])->name('warga.show');
    });
});

require __DIR__.'/auth.php';
