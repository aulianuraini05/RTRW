<?php

use App\Models\Aspiration;
use App\Models\Rt;
use App\Models\User;

test('aktivitas tercatat saat warga mengajukan aspirasi', function () {
    $rt = Rt::factory()->create();
    $warga = User::factory()->create(['role' => 'warga', 'rt_id' => $rt->id]);

    $this->actingAs($warga)->post(route('aspirations.store'), [
        'aspiration_title' => 'Lampu Jalan Mati',
        'aspiration_content' => 'Mohon diperbaiki.',
        'category' => 'Infrastruktur',
        'submission_date' => now()->toDateString(),
    ])->assertRedirect();

    $this->actingAs($warga)->get(route('activities.index'))
        ->assertStatus(200)
        ->assertSee('Lampu Jalan Mati')
        ->assertSee('mengajukan aspirasi');
});

test('riwayat dibatasi per privasi role', function () {
    $rt1 = Rt::factory()->create();
    $rt2 = Rt::factory()->create();

    $ketua1 = User::factory()->create(['role' => 'rt', 'rt_id' => $rt1->id]);
    $warga1 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt1->id]);
    $warga2 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt2->id]);

    Aspiration::factory()->create(['user_id' => $warga1->id, 'rt_id' => $rt1->id, 'aspiration_title' => 'Aspirasi RT Satu']);
    Aspiration::factory()->create(['user_id' => $warga2->id, 'rt_id' => $rt2->id, 'aspiration_title' => 'Aspirasi RT Dua']);

    // Catat manual seolah dari alur asli (factory tidak mencatat otomatis).
    App\Services\ActivityLog::record($warga1, 'aspirasi', 'mengajukan aspirasi', 'Aspirasi RT Satu');
    App\Services\ActivityLog::record($warga2, 'aspirasi', 'mengajukan aspirasi', 'Aspirasi RT Dua');

    // Ketua RT 1: hanya riwayat RT-nya.
    $this->actingAs($ketua1)->get(route('activities.index'))
        ->assertStatus(200)
        ->assertSee('Aspirasi RT Satu')
        ->assertDontSee('Aspirasi RT Dua');

    // Warga: hanya riwayat sendiri.
    $this->actingAs($warga1)->get(route('activities.index'))
        ->assertStatus(200)
        ->assertSee('Aspirasi RT Satu')
        ->assertDontSee('Aspirasi RT Dua');
});
