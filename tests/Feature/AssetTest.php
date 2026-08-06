<?php

use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\User;

test('admin can view asset list and create asset', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('assets.create'));
    $response->assertStatus(200);

    $response = $this->actingAs($admin)->post(route('assets.store'), [
        'asset_name' => 'Tenda Hajatan',
        'asset_type' => 'Furniture',
        'quantity' => 5,
        'condition' => 'baik',
        'description' => 'Tenda kapasitas 50 orang',
    ]);

    $response->assertRedirect(route('assets.index'));
    $this->assertDatabaseHas('assets', [
        'asset_name' => 'Tenda Hajatan',
        'quantity' => 5,
    ]);
});

test('warga cannot create or edit asset', function () {
    $warga = User::factory()->create(['role' => 'warga']);
    $asset = Asset::factory()->create();

    $this->actingAs($warga)->get(route('assets.create'))->assertStatus(403);
    $this->actingAs($warga)->post(route('assets.store'), [
        'asset_name' => 'Kursi Lipat',
        'asset_type' => 'Furniture',
        'quantity' => 10,
        'condition' => 'baik',
    ])->assertStatus(403);

    $this->actingAs($warga)->get(route('assets.edit', $asset))->assertStatus(403);
    $this->actingAs($warga)->delete(route('assets.destroy', $asset))->assertStatus(403);
});

test('warga can request loan for an asset', function () {
    $warga = User::factory()->create(['role' => 'warga']);
    $asset = Asset::factory()->create(['quantity' => 10]);

    $response = $this->actingAs($warga)->post(route('asset-loans.store', $asset), [
        'quantity' => 2,
        'borrow_date' => now()->toDateString(),
        'return_date' => now()->addDays(2)->toDateString(),
        'notes' => 'Untuk acara keluarga',
    ]);

    $response->assertRedirect(route('assets.show', $asset));
    $this->assertDatabaseHas('asset_loans', [
        'asset_id' => $asset->id,
        'user_id' => $warga->id,
        'quantity' => 2,
        'loan_status' => 'diajukan',
    ]);
});

test('warga cannot borrow more than available quantity', function () {
    $warga = User::factory()->create(['role' => 'warga']);
    $asset = Asset::factory()->create(['quantity' => 3]);

    $response = $this->actingAs($warga)->post(route('asset-loans.store', $asset), [
        'quantity' => 5,
        'borrow_date' => now()->toDateString(),
        'return_date' => now()->addDays(2)->toDateString(),
    ]);

    $response->assertSessionHasErrors(['quantity']);
    $this->assertDatabaseMissing('asset_loans', [
        'asset_id' => $asset->id,
        'user_id' => $warga->id,
    ]);
});

test('admin can update loan status to approved and returned', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $warga = User::factory()->create(['role' => 'warga']);
    $asset = Asset::factory()->create(['quantity' => 10]);

    $loan = AssetLoan::create([
        'asset_id' => $asset->id,
        'user_id' => $warga->id,
        'quantity' => 2,
        'borrow_date' => now()->toDateString(),
        'return_date' => now()->addDays(2)->toDateString(),
        'loan_status' => 'diajukan',
    ]);

    // Admin approves loan
    $response = $this->actingAs($admin)->patch(route('asset-loans.status.update', $loan), [
        'loan_status' => 'disetujui',
    ]);
    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('asset_loans', [
        'id' => $loan->id,
        'loan_status' => 'disetujui',
    ]);

    // Check available quantity reduced
    expect($asset->fresh()->availableQuantity())->toBe(8);

    // Admin marks returned
    $response = $this->actingAs($admin)->patch(route('asset-loans.status.update', $loan), [
        'loan_status' => 'dikembalikan',
    ]);
    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('asset_loans', [
        'id' => $loan->id,
        'loan_status' => 'dikembalikan',
    ]);

    // Check available quantity restored
    expect($asset->fresh()->availableQuantity())->toBe(10);
});
