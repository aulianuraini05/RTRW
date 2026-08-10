<?php

use App\Models\Marketplace;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('warga dan admin dapat melihat katalog produk marketplace', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $seller = User::factory()->create(['role' => 'warga']);

    Marketplace::create([
        'user_id' => $seller->id,
        'product_name' => 'Keripik Pisang',
        'description' => 'Keripik buatan warga RT',
        'price' => 25000,
        'stock' => 10,
        'product_status' => 'tersedia',
    ]);

    $this->actingAs($admin)->get(route('marketplaces.index'))
        ->assertStatus(200)
        ->assertSee('Keripik Pisang');

    $this->actingAs($seller)->get(route('marketplaces.index'))
        ->assertStatus(200)
        ->assertSee('Keripik Pisang');
});

test('warga dapat mendaftarkan produk baru di marketplace', function () {
    Storage::fake('public');

    $seller = User::factory()->create(['role' => 'warga']);

    $response = $this->actingAs($seller)->post(route('marketplaces.store'), [
        'product_name' => 'Keripik Pisang',
        'description' => 'Keripik buatan warga RT',
        'price' => 25000,
        'stock' => 10,
        'seller_phone' => '0812-3456-7890',
        'image' => UploadedFile::fake()->image('keripik.jpg', 400, 400),
    ]);

    $response->assertRedirect(route('marketplaces.index'));
    $this->assertDatabaseHas('marketplaces', [
        'user_id' => $seller->id,
        'product_name' => 'Keripik Pisang',
        'price' => 25000,
        'stock' => 10,
        'product_status' => 'tersedia',
    ]);

    $marketplace = Marketplace::where('product_name', 'Keripik Pisang')->first();
    Storage::disk('public')->assertExists($marketplace->image);
});

test('penjual hanya dapat mengedit produk miliknya sendiri', function () {
    Storage::fake('public');

    $seller = User::factory()->create(['role' => 'warga']);
    $other = User::factory()->create(['role' => 'warga']);

    $mine = Marketplace::create([
        'user_id' => $seller->id,
        'product_name' => 'Produk Saya',
        'description' => 'Milik saya',
        'price' => 10000,
        'stock' => 5,
        'product_status' => 'tersedia',
        'image' => 'marketplace/lama.jpg',
    ]);

    $theirs = Marketplace::create([
        'user_id' => $other->id,
        'product_name' => 'Produk Orang',
        'description' => 'Milik orang lain',
        'price' => 20000,
        'stock' => 5,
        'product_status' => 'tersedia',
    ]);

    $this->actingAs($seller)->get(route('marketplaces.edit', $mine))->assertStatus(200);
    $this->actingAs($seller)->get(route('marketplaces.edit', $theirs))->assertStatus(403);

    $this->actingAs($seller)->put(route('marketplaces.update', $mine), [
        'product_name' => 'Produk Saya Update',
        'description' => 'Milik saya',
        'price' => 12000,
        'stock' => 3,
        'product_status' => 'tersedia',
        'image' => UploadedFile::fake()->image('baru.jpg', 400, 400),
    ])->assertRedirect(route('marketplaces.index'));

    $mine->refresh();
    $this->assertNotNull($mine->image);
    $this->assertNotSame('marketplace/lama.jpg', $mine->image);
    Storage::disk('public')->assertMissing('marketplace/lama.jpg');
    Storage::disk('public')->assertExists($mine->image);

    $this->actingAs($seller)->put(route('marketplaces.update', $theirs), [
        'product_name' => 'Bukan Punya Saya',
        'description' => 'Milik orang lain',
        'price' => 20000,
        'stock' => 5,
    ])->assertStatus(403);
});

test('admin dapat menghapus produk marketplace', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $seller = User::factory()->create(['role' => 'warga']);

    $product = Marketplace::create([
        'user_id' => $seller->id,
        'product_name' => 'Produk Dihapus',
        'description' => 'Akan dihapus',
        'price' => 15000,
        'stock' => 2,
        'product_status' => 'tersedia',
    ]);

    $this->actingAs($admin)->delete(route('marketplaces.destroy', $product))
        ->assertRedirect(route('marketplaces.index'));
    $this->assertDatabaseMissing('marketplaces', ['id' => $product->id]);
});

test('warga tidak dapat menghapus produk milik orang lain', function () {
    $seller = User::factory()->create(['role' => 'warga']);
    $other = User::factory()->create(['role' => 'warga']);

    $theirs = Marketplace::create([
        'user_id' => $other->id,
        'product_name' => 'Produk Orang',
        'description' => 'Milik orang lain',
        'price' => 20000,
        'stock' => 5,
        'product_status' => 'tersedia',
    ]);

    $this->actingAs($seller)->delete(route('marketplaces.destroy', $theirs))->assertStatus(403);
});

test('warga dapat membeli produk dan stok berkurang', function () {
    $buyer = User::factory()->create(['role' => 'warga']);
    $seller = User::factory()->create(['role' => 'warga']);

    $product = Marketplace::create([
        'user_id' => $seller->id,
        'product_name' => 'Keripik Pisang',
        'description' => 'Keripik buatan warga RT',
        'price' => 25000,
        'stock' => 1,
        'product_status' => 'tersedia',
    ]);

    $response = $this->actingAs($buyer)->post(route('marketplaces.buy', $product));
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $product->refresh();
    expect($product->stock)->toBe(0);
    expect($product->product_status)->toBe('habis');
});

test('produk dengan gambar melebihi batas 10MB ditolak dengan pesan indonesia', function () {
    $seller = User::factory()->create(['role' => 'warga']);

    $response = $this->actingAs($seller)->post(route('marketplaces.store'), [
        'product_name' => 'Produk Besar',
        'description' => 'Foto terlalu besar',
        'price' => 15000,
        'stock' => 3,
        'image' => UploadedFile::fake()->image('besar.jpg', 400, 400)->size(11000),
    ]);

    $response->assertSessionHasErrors('image');
    $response->assertSessionHasErrors(['image' => 'Ukuran foto maksimal 10 MB.']);
});

test('produk dengan gambar hingga 10MB dapat disimpan', function () {
    Storage::fake('public');

    $seller = User::factory()->create(['role' => 'warga']);

    $response = $this->actingAs($seller)->post(route('marketplaces.store'), [
        'product_name' => 'Produk 10MB',
        'description' => 'Foto maksimal',
        'price' => 20000,
        'stock' => 5,
        'image' => UploadedFile::fake()->image('maks.jpg', 400, 400)->size(10240),
    ]);

    $response->assertRedirect(route('marketplaces.index'));
    $this->assertDatabaseHas('marketplaces', ['product_name' => 'Produk 10MB']);
});

test('produk dengan stok habis tidak dapat dibeli', function () {
    $buyer = User::factory()->create(['role' => 'warga']);
    $seller = User::factory()->create(['role' => 'warga']);

    $product = Marketplace::create([
        'user_id' => $seller->id,
        'product_name' => 'Produk Habis',
        'description' => 'Stok kosong',
        'price' => 10000,
        'stock' => 0,
        'product_status' => 'habis',
    ]);

    $response = $this->actingAs($buyer)->post(route('marketplaces.buy', $product));
    $response->assertSessionHas('error');
    $product->refresh();
    expect($product->stock)->toBe(0);
});