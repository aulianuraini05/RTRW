<?php

use App\Models\Announcement;
use App\Models\Rt;
use App\Models\User;

test('creator can open readers list from eye icon', function () {
    $rt = Rt::factory()->create();
    $ketua = User::factory()->create(['role' => 'rt', 'rt_id' => $rt->id]);
    $warga = User::factory()->create(['role' => 'warga', 'rt_id' => $rt->id]);

    $announcement = Announcement::factory()->create(['created_by' => $ketua->id]);
    $announcement->readBy()->attach($warga->id, ['read_at' => now()]);

    $this->actingAs($ketua)->get(route('announcements.readers', $announcement))
        ->assertStatus(200)
        ->assertSee($warga->name);
});

test('ketua rt cannot open readers of another announcement', function () {
    $rt1 = Rt::factory()->create();
    $rt2 = Rt::factory()->create();

    $ketua1 = User::factory()->create(['role' => 'rt', 'rt_id' => $rt1->id]);
    $ketua2 = User::factory()->create(['role' => 'rt', 'rt_id' => $rt2->id]);

    $announcement = Announcement::factory()->create(['created_by' => $ketua2->id]);

    $this->actingAs($ketua1)->get(route('announcements.readers', $announcement))->assertStatus(403);
});

test('warga cannot open readers list', function () {
    $warga = User::factory()->create(['role' => 'warga']);
    $announcement = Announcement::factory()->create();

    $this->actingAs($warga)->get(route('announcements.readers', $announcement))->assertStatus(403);
});

test('readers list shows empty state when nobody has read', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $announcement = Announcement::factory()->create();

    $this->actingAs($admin)->get(route('announcements.readers', $announcement))
        ->assertStatus(200)
        ->assertSee('Belum ada warga yang membaca');
});
