<?php

use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\Rt;
use App\Models\User;

test('ketua rt can confirm loan on own rt asset', function () {
    $rt = Rt::factory()->create();
    $ketua = User::factory()->create(['role' => 'rt', 'rt_id' => $rt->id]);
    $warga = User::factory()->create(['role' => 'warga', 'rt_id' => $rt->id]);

    $asset = Asset::factory()->create(['rt_id' => $rt->id]);
    $loan = AssetLoan::factory()->create([
        'asset_id' => $asset->id, 'user_id' => $warga->id, 'loan_status' => 'diajukan',
    ]);

    $this->actingAs($ketua)->patch(route('asset-loans.status.update', $loan), [
        'loan_status' => 'disetujui',
    ])->assertRedirect();

    $this->assertDatabaseHas('asset_loans', ['id' => $loan->id, 'loan_status' => 'disetujui']);
});

test('other ketua rt cannot confirm loan on foreign rt asset', function () {
    $rt1 = Rt::factory()->create();
    $rt2 = Rt::factory()->create();

    $ketua2 = User::factory()->create(['role' => 'rt', 'rt_id' => $rt2->id]);
    $warga1 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt1->id]);

    $asset = Asset::factory()->create(['rt_id' => $rt1->id]);
    $loan = AssetLoan::factory()->create([
        'asset_id' => $asset->id, 'user_id' => $warga1->id, 'loan_status' => 'diajukan',
    ]);

    $this->actingAs($ketua2)->patch(route('asset-loans.status.update', $loan), [
        'loan_status' => 'disetujui',
    ])->assertStatus(403);

    $this->assertDatabaseHas('asset_loans', ['id' => $loan->id, 'loan_status' => 'diajukan']);
});

test('rw cannot confirm loan on rt asset but can on public asset', function () {
    $rt = Rt::factory()->create();
    $rw = User::factory()->create(['role' => 'rw']);
    $warga = User::factory()->create(['role' => 'warga', 'rt_id' => $rt->id]);

    $rtAsset = Asset::factory()->create(['rt_id' => $rt->id]);
    $rtLoan = AssetLoan::factory()->create([
        'asset_id' => $rtAsset->id, 'user_id' => $warga->id, 'loan_status' => 'diajukan',
    ]);

    $this->actingAs($rw)->patch(route('asset-loans.status.update', $rtLoan), [
        'loan_status' => 'disetujui',
    ])->assertStatus(403);

    $publicAsset = Asset::factory()->create(['rt_id' => null]);
    $publicLoan = AssetLoan::factory()->create([
        'asset_id' => $publicAsset->id, 'user_id' => $warga->id, 'loan_status' => 'diajukan',
    ]);

    $this->actingAs($rw)->patch(route('asset-loans.status.update', $publicLoan), [
        'loan_status' => 'disetujui',
    ])->assertRedirect();

    $this->assertDatabaseHas('asset_loans', ['id' => $publicLoan->id, 'loan_status' => 'disetujui']);
});

test('ketua rt cannot confirm loan on public rw asset', function () {
    $rt = Rt::factory()->create();
    $ketua = User::factory()->create(['role' => 'rt', 'rt_id' => $rt->id]);
    $warga = User::factory()->create(['role' => 'warga', 'rt_id' => $rt->id]);

    $publicAsset = Asset::factory()->create(['rt_id' => null]);
    $loan = AssetLoan::factory()->create([
        'asset_id' => $publicAsset->id, 'user_id' => $warga->id, 'loan_status' => 'diajukan',
    ]);

    $this->actingAs($ketua)->patch(route('asset-loans.status.update', $loan), [
        'loan_status' => 'disetujui',
    ])->assertStatus(403);
});

test('ketua rt loan list only shows own rt asset loans', function () {
    $rt1 = Rt::factory()->create();
    $rt2 = Rt::factory()->create();

    $ketua1 = User::factory()->create(['role' => 'rt', 'rt_id' => $rt1->id]);
    $warga1 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt1->id]);
    $warga2 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt2->id]);

    $asset1 = Asset::factory()->create(['rt_id' => $rt1->id, 'asset_name' => 'Tenda RT Satu']);
    $asset2 = Asset::factory()->create(['rt_id' => $rt2->id, 'asset_name' => 'Tenda RT Dua']);

    AssetLoan::factory()->create(['asset_id' => $asset1->id, 'user_id' => $warga1->id]);
    AssetLoan::factory()->create(['asset_id' => $asset2->id, 'user_id' => $warga2->id]);

    $this->actingAs($ketua1)->get(route('assets.index'))
        ->assertStatus(200)
        ->assertSee('Tenda RT Satu')
        ->assertDontSee('Tenda RT Dua');
});
