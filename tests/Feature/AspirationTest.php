<?php

use App\Models\Aspiration;
use App\Models\User;

it('warga hanya melihat aspirasi miliknya sendiri', function () {
    $warga = User::factory()->create(['role' => 'warga']);
    $milikWarga = Aspiration::factory()->create(['user_id' => $warga->id, 'aspiration_title' => 'Aspirasi saya']);
    $milikOrangLain = Aspiration::factory()->create(['aspiration_title' => 'Aspirasi orang lain']);

    $this->actingAs($warga)
        ->get(route('aspirations.index'))
        ->assertOk()
        ->assertSee($milikWarga->aspiration_title)
        ->assertDontSee($milikOrangLain->aspiration_title);
});

it('warga dapat mengirim aspirasi yang terhubung ke akunnya', function () {
    $warga = User::factory()->create(['role' => 'warga']);

    $this->actingAs($warga)
        ->post(route('aspirations.store'), [
            'aspiration_title' => 'Lampu jalan mati',
            'aspiration_content' => 'Lampu di dekat pos ronda sudah mati.',
            'category' => 'Keamanan',
            'submission_date' => now()->toDateString(),
        ])
        ->assertRedirect(route('aspirations.index'));

    $this->assertDatabaseHas('aspirations', [
        'user_id' => $warga->id,
        'aspiration_title' => 'Lampu jalan mati',
        'aspiration_status' => 'dikirim',
    ]);
});

it('admin dapat melihat semua aspirasi', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $aspiration = Aspiration::factory()->create(['aspiration_title' => 'Aspirasi warga']);

    $this->actingAs($admin)
        ->get(route('aspirations.index'))
        ->assertOk()
        ->assertSee($aspiration->aspiration_title);
});

it('warga tidak dapat melihat aspirasi milik warga lain', function () {
    $warga = User::factory()->create(['role' => 'warga']);
    $aspiration = Aspiration::factory()->create();

    $this->actingAs($warga)
        ->get(route('aspirations.show', $aspiration))
        ->assertNotFound();
});

it('admin dapat mengubah status aspirasi sesuai alur', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $aspiration = Aspiration::factory()->create(['aspiration_status' => 'dikirim']);

    $this->actingAs($admin)
        ->patch(route('aspirations.status.update', $aspiration), [
            'aspiration_status' => 'diterima',
        ])
        ->assertSessionHas('success');

    expect($aspiration->fresh()->aspiration_status)->toBe('diterima');
});

it('warga tidak dapat mengubah status aspirasi', function () {
    $warga = User::factory()->create(['role' => 'warga']);
    $aspiration = Aspiration::factory()->create(['user_id' => $warga->id]);

    $this->actingAs($warga)
        ->patch(route('aspirations.status.update', $aspiration), [
            'aspiration_status' => 'diterima',
        ])
        ->assertForbidden();
});

it('admin dapat mengoreksi status aspirasi melalui pilihan ubah status', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $aspiration = Aspiration::factory()->create(['aspiration_status' => 'dikirim']);

    $this->actingAs($admin)
        ->patch(route('aspirations.status.update', $aspiration), [
            'aspiration_status' => 'selesai',
        ])
        ->assertSessionHas('success');

    expect($aspiration->fresh()->aspiration_status)->toBe('selesai');
});
