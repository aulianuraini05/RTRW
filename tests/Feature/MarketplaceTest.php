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
        'seller_phone' => '0812-3456-7890',
        'image' => UploadedFile::fake()->image('keripik.jpg', 400, 400),
    ]);

    $response->assertRedirect(route('marketplaces.index'));
    $this->assertDatabaseHas('marketplaces', [
        'user_id' => $seller->id,
        'product_name' => 'Keripik Pisang',
        'price' => 25000,
        'product_status' => 'tersedia',
        'seller_phone' => '0812-3456-7890',
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
        'product_status' => 'tersedia',
        'image' => 'marketplace/lama.jpg',
    ]);

    $theirs = Marketplace::create([
        'user_id' => $other->id,
        'product_name' => 'Produk Orang',
        'description' => 'Milik orang lain',
        'price' => 20000,
        'product_status' => 'tersedia',
    ]);

    $this->actingAs($seller)->get(route('marketplaces.edit', $mine))->assertStatus(200);
    $this->actingAs($seller)->get(route('marketplaces.edit', $theirs))->assertStatus(403);

    $this->actingAs($seller)->put(route('marketplaces.update', $mine), [
        'product_name' => 'Produk Saya Update',
        'description' => 'Milik saya',
        'price' => 12000,
        'product_status' => 'tersedia',
        'seller_phone' => '0812-3456-7890',
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
        'seller_phone' => '0812-3456-7890',
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
        'product_status' => 'tersedia',
    ]);

    $this->actingAs($seller)->delete(route('marketplaces.destroy', $theirs))->assertStatus(403);
});

test('produk dengan gambar melebihi batas 10MB ditolak dengan pesan indonesia', function () {
    $seller = User::factory()->create(['role' => 'warga']);

    $response = $this->actingAs($seller)->post(route('marketplaces.store'), [
        'product_name' => 'Produk Besar',
        'description' => 'Foto terlalu besar',
        'price' => 15000,
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
        'image' => UploadedFile::fake()->image('maks.jpg', 400, 400)->size(10240),
    ]);

    $response->assertRedirect(route('marketplaces.index'));
    $this->assertDatabaseHas('marketplaces', ['product_name' => 'Produk 10MB']);
});

test('tombol beli menampilkan link WhatsApp dengan template pesan', function () {
    $seller = User::factory()->create(['role' => 'warga', 'name' => 'Budi']);

    $product = Marketplace::create([
        'user_id' => $seller->id,
        'product_name' => 'Kopi Tubruk',
        'description' => 'Kopi buatan warga',
        'price' => 15000,
        'product_status' => 'tersedia',
        'seller_phone' => '0812-3456-7890',
    ]);

    $this->actingAs($seller)->get(route('marketplaces.show', $product))
        ->assertStatus(200)
        ->assertSee('Beli via WhatsApp')
        ->assertSee('wa.me/6281234567890', false)
        ->assertSee('Kopi Tubruk');

    $waLink = $product->whatsappLink($product->whatsappMessage());
    expect($waLink)->toStartWith('https://wa.me/6281234567890?text=');
    expect(urldecode($waLink))->toContain('Halo Budi');
});

test('produk tanpa nomor WhatsApp tidak menampilkan tombol beli', function () {
    $seller = User::factory()->create(['role' => 'warga']);

    $product = Marketplace::create([
        'user_id' => $seller->id,
        'product_name' => 'Tanpa Kontak',
        'description' => 'Tanpa nomor',
        'price' => 5000,
        'product_status' => 'tersedia',
    ]);

    $this->actingAs($seller)->get(route('marketplaces.show', $product))
        ->assertStatus(200)
        ->assertSee('Kontak penjual belum diisi')
        ->assertDontSee('wa.me');
});
