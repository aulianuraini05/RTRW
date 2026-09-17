<?php

use App\Models\Contribution;
use App\Models\Rt;
use App\Models\User;

test('ketua rt only sees iuran of own rt', function () {
    $rt1 = Rt::factory()->create();
    $rt2 = Rt::factory()->create();

    $ketua1 = User::factory()->create(['role' => 'rt', 'rt_id' => $rt1->id]);
    $warga1 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt1->id]);
    $warga2 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt2->id]);

    $c1 = Contribution::create([
        'user_id' => $warga1->id, 'rt_id' => $rt1->id, 'payer_name' => $warga1->name,
        'amount' => 15000, 'payment_status' => 'lunas',
    ]);
    $c2 = Contribution::create([
        'user_id' => $warga2->id, 'rt_id' => $rt2->id, 'payer_name' => $warga2->name,
        'amount' => 15000, 'payment_status' => 'lunas',
    ]);

    // Daftar: hanya iuran RT sendiri yang tampil.
    $this->actingAs($ketua1)->get(route('contributions.index'))
        ->assertStatus(200)
        ->assertSee($warga1->name)
        ->assertDontSee($warga2->name);

    // Detail milik RT lain: tidak bisa dibuka.
    $this->actingAs($ketua1)->get(route('contributions.show', $c2))->assertStatus(404);
    $this->actingAs($ketua1)->get(route('contributions.show', $c1))->assertStatus(200);

    // Edit / hapus / ubah status milik RT lain: ditolak.
    $this->actingAs($ketua1)->get(route('contributions.edit', $c2))->assertStatus(404);
    $this->actingAs($ketua1)->patch(route('contributions.status.update', $c2), [
        'payment_status' => 'lunas',
    ])->assertStatus(404);
    $this->actingAs($ketua1)->delete(route('contributions.destroy', $c2))->assertStatus(404);
});

test('manual iuran record by ketua rt belongs to own rt and hidden from other rt', function () {
    $rt1 = Rt::factory()->create();
    $rt2 = Rt::factory()->create();

    $ketua1 = User::factory()->create(['role' => 'rt', 'rt_id' => $rt1->id]);
    $ketua2 = User::factory()->create(['role' => 'rt', 'rt_id' => $rt2->id]);

    $this->actingAs($ketua1)->post(route('contributions.store'), [
        'payer_name' => 'Mbah Siti',
        'amount' => 15000,
        'payment_method' => 'cash',
        'payment_status' => 'lunas',
    ])->assertRedirect(route('contributions.index'));

    $this->assertDatabaseHas('contributions', [
        'payer_name' => 'Mbah Siti',
        'rt_id' => $rt1->id,
    ]);

    $this->actingAs($ketua1)->get(route('contributions.index'))->assertSee('Mbah Siti');
    $this->actingAs($ketua2)->get(route('contributions.index'))->assertDontSee('Mbah Siti');
});

test('higher admin still sees iuran from all rt', function () {
    $rt1 = Rt::factory()->create();
    $rt2 = Rt::factory()->create();

    $admin = User::factory()->create(['role' => 'admin']);
    $warga1 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt1->id]);
    $warga2 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt2->id]);

    Contribution::create([
        'user_id' => $warga1->id, 'rt_id' => $rt1->id, 'payer_name' => $warga1->name,
        'payment_status' => 'lunas',
    ]);
    Contribution::create([
        'user_id' => $warga2->id, 'rt_id' => $rt2->id, 'payer_name' => $warga2->name,
        'payment_status' => 'pending',
    ]);

    $this->actingAs($admin)->get(route('contributions.index'))
        ->assertStatus(200)
        ->assertSee($warga1->name)
        ->assertSee($warga2->name);
});
