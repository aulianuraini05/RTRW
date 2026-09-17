<?php

use App\Models\Letter;
use App\Models\Rt;
use App\Models\User;

test('ketua rt only sees letters of own rt warga', function () {
    $rt1 = Rt::factory()->create();
    $rt2 = Rt::factory()->create();

    $ketua1 = User::factory()->create(['role' => 'rt', 'rt_id' => $rt1->id]);
    $warga1 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt1->id]);
    $warga2 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt2->id]);

    $l1 = Letter::factory()->create(['user_id' => $warga1->id, 'letter_type' => 'Surat Keterangan Domisili']);
    $l2 = Letter::factory()->create(['user_id' => $warga2->id, 'letter_type' => 'Surat Pengantar KTP']);

    $this->actingAs($ketua1)->get(route('letters.index'))
        ->assertStatus(200)
        ->assertSee('Surat Keterangan Domisili')
        ->assertDontSee('Surat Pengantar KTP');

    $this->actingAs($ketua1)->get(route('letters.show', $l1))->assertStatus(200);
    $this->actingAs($ketua1)->get(route('letters.show', $l2))->assertStatus(404);
});

test('ketua rt cannot change or delete foreign rt letter', function () {
    $rt1 = Rt::factory()->create();
    $rt2 = Rt::factory()->create();

    $ketua1 = User::factory()->create(['role' => 'rt', 'rt_id' => $rt1->id]);
    $warga2 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt2->id]);

    $l2 = Letter::factory()->create(['user_id' => $warga2->id]);

    $this->actingAs($ketua1)->patch(route('letters.status.update', $l2), [
        'letter_status' => 'disetujui',
    ])->assertStatus(404);

    $this->actingAs($ketua1)->delete(route('letters.destroy', $l2))->assertStatus(404);

    $this->assertDatabaseHas('letters', ['id' => $l2->id, 'letter_status' => 'diajukan']);
});

test('higher admin still sees all letters', function () {
    $rt1 = Rt::factory()->create();
    $rt2 = Rt::factory()->create();

    $admin = User::factory()->create(['role' => 'admin']);
    $warga1 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt1->id]);
    $warga2 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt2->id]);

    Letter::factory()->create(['user_id' => $warga1->id, 'letter_type' => 'Surat Keterangan Usaha']);
    Letter::factory()->create(['user_id' => $warga2->id, 'letter_type' => 'Surat Pengantar Nikah']);

    $this->actingAs($admin)->get(route('letters.index'))
        ->assertStatus(200)
        ->assertSee('Surat Keterangan Usaha')
        ->assertSee('Surat Pengantar Nikah');
});
