<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AspirationController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetLoanController;
use App\Http\Controllers\CashTransactionController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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
        Route::get('/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
        Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
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

    // Warga & Admin: lihat detail
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

    // Admin only: kelola transaksi kas
    Route::middleware('role:admin')->group(function () {
        Route::get('/cash-transactions/create', [CashTransactionController::class, 'create'])->name('cash_transactions.create');
        Route::post('/cash-transactions', [CashTransactionController::class, 'store'])->name('cash_transactions.store');
        Route::get('/cash-transactions/{cashTransaction}/edit', [CashTransactionController::class, 'edit'])->name('cash_transactions.edit');
        Route::put('/cash-transactions/{cashTransaction}', [CashTransactionController::class, 'update'])->name('cash_transactions.update');
        Route::delete('/cash-transactions/{cashTransaction}', [CashTransactionController::class, 'destroy'])->name('cash_transactions.destroy');
    });

    // Warga & Admin: lihat detail
    Route::get('/cash-transactions/{cashTransaction}', [CashTransactionController::class, 'show'])->name('cash_transactions.show');

    // =========================================================================
    // IURAN WARGA (Contribution)
    // =========================================================================
    Route::get('/contributions', [ContributionController::class, 'index'])->name('contributions.index');

    // Admin only: kelola iuran
    Route::middleware('role:admin')->group(function () {
        Route::get('/contributions/create', [ContributionController::class, 'create'])->name('contributions.create');
        Route::post('/contributions', [ContributionController::class, 'store'])->name('contributions.store');
        Route::get('/contributions/{contribution}/edit', [ContributionController::class, 'edit'])->name('contributions.edit');
        Route::put('/contributions/{contribution}', [ContributionController::class, 'update'])->name('contributions.update');
        Route::delete('/contributions/{contribution}', [ContributionController::class, 'destroy'])->name('contributions.destroy');
    });

    // Warga & Admin: lihat detail
    Route::get('/contributions/{contribution}', [ContributionController::class, 'show'])->name('contributions.show');

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
});

require __DIR__.'/auth.php';
