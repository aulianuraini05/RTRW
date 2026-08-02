<?php

use App\Models\Announcement;
use App\Models\User;

it('warga hanya melihat pengumuman aktif', function () {
    $warga = User::factory()->create(['role' => 'warga']);
    $active = Announcement::factory()->create(['status' => 'active']);
    $archived = Announcement::factory()->create(['status' => 'archived']);

    $this->actingAs($warga)
        ->get(route('announcements.index'))
        ->assertOk()
        ->assertSee($active->announcement_title)
        ->assertDontSee($archived->announcement_title);
});

it('admin dapat membuat pengumuman', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->post(route('announcements.store'), [
            'announcement_title' => 'Kerja Bakti Mingguan',
            'announcement_content' => 'Kerja bakti pada hari Minggu pagi.',
            'publication_date' => '2026-08-03',
            'status' => 'active',
        ])
        ->assertRedirect(route('announcements.index'));

    $this->assertDatabaseHas('announcements', [
        'announcement_title' => 'Kerja Bakti Mingguan',
        'status' => 'active',
    ]);
});

it('warga tidak dapat membuat pengumuman', function () {
    $warga = User::factory()->create(['role' => 'warga']);

    $this->actingAs($warga)
        ->post(route('announcements.store'), [
            'announcement_title' => 'Tidak boleh dibuat',
            'announcement_content' => 'Konten',
            'publication_date' => '2026-08-03',
            'status' => 'active',
        ])
        ->assertForbidden();
});

it('warga tidak dapat melihat pengumuman arsip', function () {
    $warga = User::factory()->create(['role' => 'warga']);
    $archived = Announcement::factory()->create(['status' => 'archived']);

    $this->actingAs($warga)
        ->get(route('announcements.show', $archived))
        ->assertNotFound();
});
