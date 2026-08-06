<?php

use App\Models\CashTransaction;
use App\Models\User;

test('admin can view kas index with stats', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $warga = User::factory()->create(['role' => 'warga']);

    CashTransaction::create([
        'user_id' => $warga->id,
        'payment_status' => 'lunas',
    ]);

    CashTransaction::create([
        'user_id' => $warga->id,
        'payment_status' => 'pending',
    ]);

    $response = $this->actingAs($admin)->get(route('cash_transactions.index'));
    $response->assertStatus(200);
    $response->assertSee($warga->name);
    $response->assertSee('1 warga'); // total lunas
    $response->assertSee('1 warga'); // total pending
});

test('admin can create cash payment record', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $warga = User::factory()->create(['role' => 'warga']);

    $response = $this->actingAs($admin)->post(route('cash_transactions.store'), [
        'user_id' => $warga->id,
        'payment_status' => 'lunas',
        'proof_of_payment' => 'Transfer BCA a.n. Budi',
    ]);

    $response->assertRedirect(route('cash_transactions.index'));
    $this->assertDatabaseHas('cash_transactions', [
        'user_id' => $warga->id,
        'payment_status' => 'lunas',
        'proof_of_payment' => 'Transfer BCA a.n. Budi',
    ]);
});

test('admin can update payment status via status endpoint', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $warga = User::factory()->create(['role' => 'warga']);

    $transaction = CashTransaction::create([
        'user_id' => $warga->id,
        'payment_status' => 'pending',
    ]);

    $response = $this->actingAs($admin)->patch(route('cash_transactions.status.update', $transaction), [
        'payment_status' => 'lunas',
    ]);
    $response->assertRedirect();
    $this->assertDatabaseHas('cash_transactions', [
        'id' => $transaction->id,
        'payment_status' => 'lunas',
    ]);
});

test('admin can edit and delete cash payment record', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $warga = User::factory()->create(['role' => 'warga']);

    $transaction = CashTransaction::create([
        'user_id' => $warga->id,
        'payment_status' => 'pending',
    ]);

    $response = $this->actingAs($admin)->put(route('cash_transactions.update', $transaction), [
        'user_id' => $warga->id,
        'payment_status' => 'ditolak',
        'proof_of_payment' => 'Bukti tidak jelas',
    ]);
    $response->assertRedirect(route('cash_transactions.index'));
    $this->assertDatabaseHas('cash_transactions', [
        'id' => $transaction->id,
        'payment_status' => 'ditolak',
    ]);

    $response = $this->actingAs($admin)->delete(route('cash_transactions.destroy', $transaction));
    $response->assertRedirect(route('cash_transactions.index'));
    $this->assertDatabaseMissing('cash_transactions', [
        'id' => $transaction->id,
    ]);
});

test('warga only sees their own kas records and cannot manage', function () {
    $warga = User::factory()->create(['role' => 'warga']);
    $other = User::factory()->create(['role' => 'warga']);

    $mine = CashTransaction::create([
        'user_id' => $warga->id,
        'payment_status' => 'lunas',
    ]);

    $theirs = CashTransaction::create([
        'user_id' => $other->id,
        'payment_status' => 'pending',
    ]);

    $this->actingAs($warga)->get(route('cash_transactions.index'))
        ->assertStatus(200)
        ->assertSee($warga->name)
        ->assertDontSee($other->name);

    $this->actingAs($warga)->get(route('cash_transactions.show', $mine))->assertStatus(200);
    $this->actingAs($warga)->get(route('cash_transactions.show', $theirs))->assertStatus(404);

    $this->actingAs($warga)->get(route('cash_transactions.create'))->assertStatus(403);
    $this->actingAs($warga)->post(route('cash_transactions.store'), [
        'user_id' => $warga->id,
        'payment_status' => 'lunas',
    ])->assertStatus(403);

    $this->actingAs($warga)->get(route('cash_transactions.edit', $mine))->assertStatus(403);
    $this->actingAs($warga)->put(route('cash_transactions.update', $mine), [
        'user_id' => $warga->id,
        'payment_status' => 'lunas',
    ])->assertStatus(403);

    $this->actingAs($warga)->delete(route('cash_transactions.destroy', $mine))->assertStatus(403);
});
