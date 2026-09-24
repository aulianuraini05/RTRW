<?php

use App\Models\Rt;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('user baru bisa mendaftar sebagai warga dengan kode WARGA-RT01', function () {
    $rt = Rt::factory()->create(['code' => 'WARGA-RT01', 'admin_code' => 'KETUA-RT01']);

    $response = $this->post('/register', [
        'name' => 'Warga Test',
        'email' => 'warga@example.com',
        'no_whatsapp' => '081234567890',
        'nik' => '3201010101010001',
        'no_kk' => '3201010101010002',
        'password' => 'password',
        'password_confirmation' => 'password',
        'rt_code' => 'WARGA-RT01',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $user = auth()->user();
    expect($user->role)->toBe('warga');
    expect($user->rt_id)->toBe($rt->id);
});

test('user baru bisa mendaftar sebagai ketua RT dengan kode KETUA-RT01', function () {
    $rt = Rt::factory()->create(['code' => 'WARGA-RT01', 'admin_code' => 'KETUA-RT01']);

    $response = $this->post('/register', [
        'name' => 'Ketua RT Test',
        'email' => 'rt@example.com',
        'no_whatsapp' => '081234567891',
        'nik' => '3201010101010003',
        'no_kk' => '3201010101010004',
        'password' => 'password',
        'password_confirmation' => 'password',
        'rt_code' => 'KETUA-RT01',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $user = auth()->user();
    expect($user->role)->toBe('rt');
    expect($user->rt_id)->toBe($rt->id);
});

test('user baru bisa mendaftar sebagai ketua RW dengan kode KETUA-RW', function () {
    $response = $this->post('/register', [
        'name' => 'Pak RW Test',
        'email' => 'rw@example.com',
        'no_whatsapp' => '081234567892',
        'nik' => '3201010101010005',
        'no_kk' => '3201010101010006',
        'password' => 'password',
        'password_confirmation' => 'password',
        'rt_code' => 'KETUA-RW',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $user = auth()->user();
    expect($user->role)->toBe('rw');
    expect($user->rt_id)->toBeNull();
});

test('user baru bisa mendaftar sebagai superadmin dengan kode ADMIN-UTAMA', function () {
    $response = $this->post('/register', [
        'name' => 'Admin Utama Test',
        'email' => 'admin@example.com',
        'no_whatsapp' => '081234567893',
        'nik' => '3201010101010007',
        'no_kk' => '3201010101010008',
        'password' => 'password',
        'password_confirmation' => 'password',
        'rt_code' => 'ADMIN-UTAMA',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $user = auth()->user();
    expect($user->role)->toBe('superadmin');
    expect($user->rt_id)->toBeNull();
});

test('registration fails with invalid rt code', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'no_whatsapp' => '081234567894',
        'nik' => '3201010101010009',
        'no_kk' => '3201010101010010',
        'password' => 'password',
        'password_confirmation' => 'password',
        'rt_code' => 'KODE-SALAH-999',
    ]);

    $response->assertSessionHasErrors('rt_code');
    $this->assertGuest();
});
