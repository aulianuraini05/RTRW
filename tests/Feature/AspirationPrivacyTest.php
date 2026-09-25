<?php

use App\Models\Aspiration;
use App\Models\Rt;
use App\Models\User;

test('ketua rt only sees aspirations of own rt warga', function () {
    $rt1 = Rt::factory()->create();
    $rt2 = Rt::factory()->create();

    $ketua1 = User::factory()->create(['role' => 'rt', 'rt_id' => $rt1->id]);
    $warga1 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt1->id]);
    $warga2 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt2->id]);

    $a1 = Aspiration::factory()->create(['user_id' => $warga1->id, 'aspiration_title' => 'Lampu Jalan Mati RT Satu']);
    $a2 = Aspiration::factory()->create(['user_id' => $warga2->id, 'aspiration_title' => 'Sampah Menumpuk RT Dua']);

    $this->actingAs($ketua1)->get(route('aspirations.index'))
        ->assertStatus(200)
        ->assertSee('Lampu Jalan Mati RT Satu')
        ->assertDontSee('Sampah Menumpuk RT Dua');

    $this->actingAs($ketua1)->get(route('aspirations.show', $a1))->assertStatus(200);
    $this->actingAs($ketua1)->get(route('aspirations.show', $a2))->assertStatus(404);
});

test('ketua rt cannot change or delete foreign rt aspiration', function () {
    $rt1 = Rt::factory()->create();
    $rt2 = Rt::factory()->create();

    $ketua1 = User::factory()->create(['role' => 'rt', 'rt_id' => $rt1->id]);
    $warga2 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt2->id]);

    $a2 = Aspiration::factory()->create(['user_id' => $warga2->id]);

    $this->actingAs($ketua1)->patch(route('aspirations.status.update', $a2), [
        'aspiration_status' => 'selesai',
    ])->assertStatus(404);

    $this->actingAs($ketua1)->delete(route('aspirations.destroy', $a2))->assertStatus(404);

    $this->assertDatabaseHas('aspirations', ['id' => $a2->id, 'aspiration_status' => 'dikirim']);
});

test('ketua rt manages own rt aspirations without forward button', function () {
    $rt1 = Rt::factory()->create();

    $ketua1 = User::factory()->create(['role' => 'rt', 'rt_id' => $rt1->id]);
    $warga1 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt1->id]);

    $a1 = Aspiration::factory()->create(['user_id' => $warga1->id, 'rt_id' => $rt1->id, 'aspiration_status' => 'dikirim']);

    // Tidak ada lagi tombol forward — aspirasi langsung terlihat RW.
    $this->actingAs($ketua1)->get(route('aspirations.index'))
        ->assertStatus(200)
        ->assertDontSee('Teruskan ke RW');
    $this->actingAs($ketua1)->get(route('aspirations.show', $a1))
        ->assertStatus(200)
        ->assertDontSee('Teruskan ke RW');

    // RT tetap bisa memproses seperti biasa.
    $this->actingAs($ketua1)->patch(route('aspirations.status.update', $a1), [
        'aspiration_status' => 'diproses',
    ])->assertRedirect();

    expect($a1->fresh()->aspiration_status)->toBe('diproses');
});

test('aspirasi tetap terlihat oleh rt walau akun warga dihapus', function () {
    $rt1 = Rt::factory()->create();

    $ketua1 = User::factory()->create(['role' => 'rt', 'rt_id' => $rt1->id]);
    $warga1 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt1->id]);

    // Lewat alur asli agar rt_id tercatat saat pengajuan.
    $this->actingAs($warga1)->post(route('aspirations.store'), [
        'aspiration_title' => 'Jalan Rusak Parah',
        'aspiration_content' => 'Mohon diperbaiki.',
        'category' => 'Infrastruktur',
        'submission_date' => now()->toDateString(),
    ])->assertRedirect();

    $asp = Aspiration::where('aspiration_title', 'Jalan Rusak Parah')->firstOrFail();

    // Warga dihapus (mis. keluar/pindah) — aspirasi harus tetap ada untuk RT.
    $warga1->delete();

    $this->actingAs($ketua1)->get(route('aspirations.index'))
        ->assertStatus(200)
        ->assertSee('Jalan Rusak Parah');
    $this->actingAs($ketua1)->get(route('aspirations.show', $asp))->assertStatus(200);
});

test('higher admin still sees all aspirations', function () {
    $rt1 = Rt::factory()->create();
    $rt2 = Rt::factory()->create();

    $admin = User::factory()->create(['role' => 'admin']);
    $warga1 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt1->id]);
    $warga2 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt2->id]);

    Aspiration::factory()->create(['user_id' => $warga1->id, 'aspiration_title' => 'Aspirasi RT Satu']);
    Aspiration::factory()->create(['user_id' => $warga2->id, 'aspiration_title' => 'Aspirasi RT Dua']);

    $this->actingAs($admin)->get(route('aspirations.index'))
        ->assertStatus(200)
        ->assertSee('Aspirasi RT Satu')
        ->assertSee('Aspirasi RT Dua');
});
