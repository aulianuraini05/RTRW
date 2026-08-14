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
            'category' => 'kegiatan',
            'priority' => 'penting',
            'is_pinned' => 1,
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

it('admin melihat panel kelola pengumuman dengan statistik dan tabel', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Announcement::factory()->create(['status' => 'active', 'category' => 'kesehatan', 'priority' => 'mendesak', 'is_pinned' => true, 'read_count' => 12]);

    $this->actingAs($admin)
        ->get(route('announcements.index'))
        ->assertOk()
        ->assertSee('Kelola Pengumuman')
        ->assertSee('Total Pengumuman')
        ->assertSee('Total Dibaca')
        ->assertSee('Buat Baru')
        ->assertSee('Kesehatan')
        ->assertSee('Mendesak');
});

it('warga tetap melihat daftar pengumuman sederhana', function () {
    $warga = User::factory()->create(['role' => 'warga']);
    Announcement::factory()->create(['status' => 'active']);

    $this->actingAs($warga)
        ->get(route('announcements.index'))
        ->assertOk()
        ->assertDontSee('Kelola Pengumuman')
        ->assertDontSee('Total Pengumuman');
});

it('admin dapat toggle status pengumuman aktif/nonaktif', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $announcement = Announcement::factory()->create(['status' => 'active']);

    $this->actingAs($admin)
        ->patch(route('announcements.toggle', $announcement))
        ->assertRedirect();

    $this->assertDatabaseHas('announcements', ['id' => $announcement->id, 'status' => 'archived']);
});

it('admin dapat memfilter pengumuman berdasarkan prioritas dan status', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $mendesak = Announcement::factory()->create(['priority' => 'mendesak', 'status' => 'active']);
    $penting = Announcement::factory()->create(['priority' => 'penting', 'status' => 'active']);
    $biasa = Announcement::factory()->create(['priority' => 'biasa', 'status' => 'active']);
    $archived = Announcement::factory()->create(['priority' => 'biasa', 'status' => 'archived']);

    $this->actingAs($admin)
        ->get(route('announcements.index', ['filter' => 'mendesak']))
        ->assertOk()
        ->assertSee($mendesak->announcement_title)
        ->assertDontSee($penting->announcement_title)
        ->assertDontSee($biasa->announcement_title)
        ->assertDontSee($archived->announcement_title);

    $this->actingAs($admin)
        ->get(route('announcements.index', ['filter' => 'penting']))
        ->assertOk()
        ->assertSee($penting->announcement_title)
        ->assertDontSee($mendesak->announcement_title);

    $this->actingAs($admin)
        ->get(route('announcements.index', ['filter' => 'biasa']))
        ->assertOk()
        ->assertSee($biasa->announcement_title)
        ->assertDontSee($mendesak->announcement_title);

    $this->actingAs($admin)
        ->get(route('announcements.index', ['filter' => 'nonaktif']))
        ->assertOk()
        ->assertSee($archived->announcement_title)
        ->assertDontSee($mendesak->announcement_title);
});

it('warga dapat memfilter pengumuman berdasarkan prioritas', function () {
    $warga = User::factory()->create(['role' => 'warga']);
    $mendesak = Announcement::factory()->create(['priority' => 'mendesak', 'status' => 'active']);
    $biasa = Announcement::factory()->create(['priority' => 'biasa', 'status' => 'active']);

    $this->actingAs($warga)
        ->get(route('announcements.index', ['filter' => 'mendesak']))
        ->assertOk()
        ->assertSee('Semua')
        ->assertSee('Mendesak')
        ->assertSee('Penting')
        ->assertSee('Biasa')
        ->assertSee($mendesak->announcement_title)
        ->assertDontSee($biasa->announcement_title);
});

it('pengumuman belum dibaca ditandai untuk warga', function () {
    $warga = User::factory()->create(['role' => 'warga']);
    $announcement = Announcement::factory()->create(['status' => 'active']);

    $this->actingAs($warga)
        ->get(route('announcements.index'))
        ->assertOk()
        ->assertSee('Belum dibaca');
});

it('pengumuman yang sudah dibaca tidak ditandai lagi', function () {
    $warga = User::factory()->create(['role' => 'warga']);
    $announcement = Announcement::factory()->create(['status' => 'active']);
    $announcement->readBy()->attach($warga->id, ['read_at' => now()]);

    $this->actingAs($warga)
        ->get(route('announcements.index'))
        ->assertOk()
        ->assertSee($announcement->announcement_title)
        ->assertDontSee('Belum dibaca');
});

it('warga menandai pengumuman terbaca saat membuka detail', function () {
    $warga = User::factory()->create(['role' => 'warga']);
    $announcement = Announcement::factory()->create(['status' => 'active', 'read_count' => 0]);

    $this->actingAs($warga)
        ->get(route('announcements.show', $announcement))
        ->assertOk();

    $this->assertDatabaseHas('announcement_reads', [
        'announcement_id' => $announcement->id,
        'user_id' => $warga->id,
    ]);

    $this->assertSame(1, $announcement->fresh()->read_count);
});
