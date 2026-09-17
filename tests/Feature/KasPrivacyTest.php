<?php

use App\Models\CashTransaction;
use App\Models\Rt;
use App\Models\User;

test('ketua rt only sees kas of own rt', function () {
    $rt1 = Rt::factory()->create();
    $rt2 = Rt::factory()->create();

    $ketua1 = User::factory()->create(['role' => 'rt', 'rt_id' => $rt1->id]);
    $warga1 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt1->id]);
    $warga2 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt2->id]);

    $tx1 = CashTransaction::create([
        'user_id' => $warga1->id, 'rt_id' => $rt1->id, 'payer_name' => $warga1->name,
        'amount' => 25000, 'payment_status' => 'lunas',
    ]);
    $tx2 = CashTransaction::create([
        'user_id' => $warga2->id, 'rt_id' => $rt2->id, 'payer_name' => $warga2->name,
        'amount' => 30000, 'payment_status' => 'lunas',
    ]);

    // Daftar: hanya kas RT sendiri yang tampil.
    $this->actingAs($ketua1)->get(route('cash_transactions.index'))
        ->assertStatus(200)
        ->assertSee($warga1->name)
        ->assertDontSee($warga2->name);

    // Detail milik RT lain: tidak bisa dibuka.
    $this->actingAs($ketua1)->get(route('cash_transactions.show', $tx2))->assertStatus(404);
    $this->actingAs($ketua1)->get(route('cash_transactions.show', $tx1))->assertStatus(200);

    // Edit / hapus / ubah status milik RT lain: ditolak.
    $this->actingAs($ketua1)->get(route('cash_transactions.edit', $tx2))->assertStatus(404);
    $this->actingAs($ketua1)->patch(route('cash_transactions.status.update', $tx2), [
        'payment_status' => 'lunas',
    ])->assertStatus(404);
    $this->actingAs($ketua1)->delete(route('cash_transactions.destroy', $tx2))->assertStatus(404);
});

test('manual record by ketua rt belongs to own rt and hidden from other rt', function () {
    $rt1 = Rt::factory()->create();
    $rt2 = Rt::factory()->create();

    $ketua1 = User::factory()->create(['role' => 'rt', 'rt_id' => $rt1->id]);
    $ketua2 = User::factory()->create(['role' => 'rt', 'rt_id' => $rt2->id]);

    $this->actingAs($ketua1)->post(route('cash_transactions.store'), [
        'payer_name' => 'Mbah Siti',
        'amount' => 25000,
        'payment_method' => 'cash',
        'payment_status' => 'lunas',
    ])->assertRedirect(route('cash_transactions.index'));

    $this->assertDatabaseHas('cash_transactions', [
        'payer_name' => 'Mbah Siti',
        'rt_id' => $rt1->id,
    ]);

    $this->actingAs($ketua1)->get(route('cash_transactions.index'))->assertSee('Mbah Siti');
    $this->actingAs($ketua2)->get(route('cash_transactions.index'))->assertDontSee('Mbah Siti');
});

test('higher admin still sees kas from all rt', function () {
    $rt1 = Rt::factory()->create();
    $rt2 = Rt::factory()->create();

    $admin = User::factory()->create(['role' => 'admin']);
    $warga1 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt1->id]);
    $warga2 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt2->id]);

    CashTransaction::create([
        'user_id' => $warga1->id, 'rt_id' => $rt1->id, 'payer_name' => $warga1->name,
        'payment_status' => 'lunas',
    ]);
    CashTransaction::create([
        'user_id' => $warga2->id, 'rt_id' => $rt2->id, 'payer_name' => $warga2->name,
        'payment_status' => 'pending',
    ]);

    $this->actingAs($admin)->get(route('cash_transactions.index'))
        ->assertStatus(200)
        ->assertSee($warga1->name)
        ->assertSee($warga2->name);
});
