<?php

use App\Models\Contribution;
use App\Models\User;

test('admin can view iuran index with stats', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $warga = User::factory()->create(['role' => 'warga']);

    Contribution::create([
        'user_id' => $warga->id,
        'payment_status' => 'lunas',
    ]);

    Contribution::create([
        'user_id' => $warga->id,
        'payment_status' => 'pending',
    ]);

    $response = $this->actingAs($admin)->get(route('contributions.index'));
    $response->assertStatus(200);
    $response->assertSee($warga->name);
});

test('admin can create iuran payment record', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $warga = User::factory()->create(['role' => 'warga']);

    $response = $this->actingAs($admin)->post(route('contributions.store'), [
        'user_id' => $warga->id,
        'payment_status' => 'lunas',
        'proof_of_payment' => 'Transfer BCA a.n. Budi',
    ]);

    $response->assertRedirect(route('contributions.index'));
    $this->assertDatabaseHas('contributions', [
        'user_id' => $warga->id,
        'payment_status' => 'lunas',
        'proof_of_payment' => 'Transfer BCA a.n. Budi',
    ]);
});

test('admin can update iuran status via status endpoint', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $warga = User::factory()->create(['role' => 'warga']);

    $contribution = Contribution::create([
        'user_id' => $warga->id,
        'payment_status' => 'pending',
    ]);

    $response = $this->actingAs($admin)->patch(route('contributions.status.update', $contribution), [
        'payment_status' => 'lunas',
    ]);
    $response->assertRedirect();
    $this->assertDatabaseHas('contributions', [
        'id' => $contribution->id,
        'payment_status' => 'lunas',
    ]);
});

test('admin can edit and delete iuran payment record', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $warga = User::factory()->create(['role' => 'warga']);

    $contribution = Contribution::create([
        'user_id' => $warga->id,
        'payment_status' => 'pending',
    ]);

    $response = $this->actingAs($admin)->put(route('contributions.update', $contribution), [
        'user_id' => $warga->id,
        'payment_status' => 'ditolak',
        'proof_of_payment' => 'Bukti tidak jelas',
    ]);
    $response->assertRedirect(route('contributions.index'));
    $this->assertDatabaseHas('contributions', [
        'id' => $contribution->id,
        'payment_status' => 'ditolak',
    ]);

    $response = $this->actingAs($admin)->delete(route('contributions.destroy', $contribution));
    $response->assertRedirect(route('contributions.index'));
    $this->assertDatabaseMissing('contributions', [
        'id' => $contribution->id,
    ]);
});

test('warga can submit their own iuran payment', function () {
    $warga = User::factory()->create(['role' => 'warga']);

    $response = $this->actingAs($warga)->post(route('contributions.store'), [
        'amount' => '50000',
        'payment_method' => 'virtual_account',
        'proof_of_payment' => 'Transfer BCA a.n. Saya',
    ]);

    $response->assertRedirect(route('contributions.index'));
    $this->assertDatabaseHas('contributions', [
        'user_id' => $warga->id,
        'amount' => 50000,
        'payment_method' => 'virtual_account',
        'payment_status' => 'pending',
        'proof_of_payment' => 'Transfer BCA a.n. Saya',
    ]);
});

test('warga can complete their pending iuran payment online', function () {
    $warga = User::factory()->create(['role' => 'warga']);

    $contribution = Contribution::create([
        'user_id' => $warga->id,
        'amount' => 50000,
        'payment_method' => 'virtual_account',
        'payment_code' => 'IUR-250813-ABCDEF',
        'payment_status' => 'pending',
    ]);

    $response = $this->actingAs($warga)->post(route('contributions.pay', $contribution));

    $response->assertRedirect();
    $this->assertDatabaseHas('contributions', [
        'id' => $contribution->id,
        'payment_status' => 'lunas',
    ]);
    $this->assertNotNull($contribution->fresh()->paid_at);
});

test('warga cannot pay online a iuran record belonging to someone else', function () {
    $warga = User::factory()->create(['role' => 'warga']);
    $other = User::factory()->create(['role' => 'warga']);

    $theirs = Contribution::create([
        'user_id' => $other->id,
        'amount' => 50000,
        'payment_method' => 'virtual_account',
        'payment_code' => 'IUR-250813-ABCDEF',
        'payment_status' => 'pending',
    ]);

    $this->actingAs($warga)->post(route('contributions.pay', $theirs))->assertStatus(404);
});

test('warga only sees their own iuran records and cannot manage others', function () {
    $warga = User::factory()->create(['role' => 'warga']);
    $other = User::factory()->create(['role' => 'warga']);

    $mine = Contribution::create([
        'user_id' => $warga->id,
        'payment_status' => 'lunas',
    ]);

    $theirs = Contribution::create([
        'user_id' => $other->id,
        'payment_status' => 'pending',
    ]);

    $this->actingAs($warga)->get(route('contributions.index'))
        ->assertStatus(200)
        ->assertSee($warga->name)
        ->assertDontSee($other->name);

    $this->actingAs($warga)->get(route('contributions.show', $mine))->assertStatus(200);
    $this->actingAs($warga)->get(route('contributions.show', $theirs))->assertStatus(404);

    $this->actingAs($warga)->get(route('contributions.edit', $mine))->assertStatus(403);
    $this->actingAs($warga)->put(route('contributions.update', $mine), [
        'user_id' => $warga->id,
        'payment_status' => 'lunas',
    ])->assertStatus(403);

    $this->actingAs($warga)->delete(route('contributions.destroy', $mine))->assertStatus(403);
    $this->actingAs($warga)->patch(route('contributions.status.update', $mine), [
        'payment_status' => 'lunas',
    ])->assertStatus(403);
});
