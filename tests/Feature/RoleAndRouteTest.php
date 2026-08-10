<?php

use App\Models\User;

/*
|--------------------------------------------------------------------------
| Test: Role pada User Model
|--------------------------------------------------------------------------
*/

it('user baru memiliki role default warga', function () {
    $user = User::factory()->create();

    expect($user->role)->toBe('warga');
});

it('user bisa diset sebagai admin', function () {
    $user = User::factory()->create(['role' => 'admin']);

    expect($user->role)->toBe('admin');
    expect($user->isAdmin())->toBeTrue();
    expect($user->isWarga())->toBeFalse();
});

it('helper isWarga() mengembalikan true untuk warga', function () {
    $user = User::factory()->create(['role' => 'warga']);

    expect($user->isWarga())->toBeTrue();
    expect($user->isAdmin())->toBeFalse();
});

/*
|--------------------------------------------------------------------------
| Test: Middleware Role - Admin Routes
|--------------------------------------------------------------------------
*/

it('admin bisa akses route admin-only', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('announcements.create'))
        ->assertStatus(200)
        ->assertOk();
});

it('warga tidak bisa akses route admin-only (403)', function () {
    $warga = User::factory()->create(['role' => 'warga']);

    $this->actingAs($warga)
        ->get(route('announcements.create'))
        ->assertStatus(403);
});

it('guest diarahkan ke login saat akses route modul', function () {
    $this->get(route('announcements.index'))
        ->assertRedirect(route('login'));
});

/*
|--------------------------------------------------------------------------
| Test: Route Semua Modul Bisa Diakses User Login
|--------------------------------------------------------------------------
*/

it('user login bisa akses halaman index semua modul', function () {
    $user = User::factory()->create();

    $routes = [
        'announcements.index',
        'aspirations.index',
        'letters.index',
        'assets.index',
        'cash_transactions.index',
        'contributions.index',
        'marketplaces.index',
    ];

    foreach ($routes as $routeName) {
        $this->actingAs($user)
            ->get(route($routeName))
            ->assertStatus(200);
    }
});

/*
|--------------------------------------------------------------------------
| Test: Admin bisa akses create di semua modul admin-only
|--------------------------------------------------------------------------
*/

it('admin bisa akses create di semua modul admin-only', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $adminCreateRoutes = [
        'announcements.create',
        'assets.create',
        'cash_transactions.create',
        'contributions.create',
    ];

    foreach ($adminCreateRoutes as $routeName) {
        $this->actingAs($admin)
            ->get(route($routeName))
            ->assertStatus(200);
    }
});

it('warga tidak bisa akses create di modul admin-only (403)', function () {
    $warga = User::factory()->create(['role' => 'warga']);

    $adminCreateRoutes = [
        'announcements.create',
        'assets.create',
    ];

    foreach ($adminCreateRoutes as $routeName) {
        $this->actingAs($warga)
            ->get(route($routeName))
            ->assertStatus(403);
    }
});

/*
|--------------------------------------------------------------------------
| Test: Warga bisa akses create di modul yang dibolehkan
|--------------------------------------------------------------------------
*/

it('warga bisa akses create aspirasi, surat, kas, iuran, dan marketplace', function () {
    $warga = User::factory()->create(['role' => 'warga']);

    $wargaCreateRoutes = [
        'aspirations.create',
        'letters.create',
        'cash_transactions.create',
        'contributions.create',
        'marketplaces.create',
    ];

    foreach ($wargaCreateRoutes as $routeName) {
        $this->actingAs($warga)
            ->get(route($routeName))
            ->assertStatus(200);
    }
});
