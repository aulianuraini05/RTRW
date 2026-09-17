<?php

use App\Models\Contribution;
use App\Models\IuranSchedule;
use App\Models\Rt;
use App\Models\User;

test('ketua rt can set monthly iuran and bills are auto generated for own rt', function () {
    $rt = Rt::factory()->create();
    $otherRt = Rt::factory()->create();

    $ketua = User::factory()->create(['role' => 'rt', 'rt_id' => $rt->id]);
    $warga1 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt->id]);
    $warga2 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt->id]);
    $outsider = User::factory()->create(['role' => 'warga', 'rt_id' => $otherRt->id]);

    $periode = now()->format('Y-m');

    $response = $this->actingAs($ketua)->post(route('iuran_schedules.store'), [
        'jenis' => 'Sampah',
        'periode' => $periode,
        'amount' => 15000,
    ]);

    $schedule = IuranSchedule::first();
    expect($schedule)->not->toBeNull();
    expect((int) $schedule->rt_id)->toBe((int) $rt->id);
    expect((float) $schedule->amount)->toBe(15000.0);

    $response->assertRedirect(route('iuran_schedules.show', $schedule));

    // Tagihan otomatis untuk warga RT sendiri saja.
    foreach ([$warga1, $warga2] as $warga) {
        $this->assertDatabaseHas('contributions', [
            'user_id' => $warga->id,
            'iuran_schedule_id' => $schedule->id,
            'amount' => 15000,
            'payment_status' => 'pending',
        ]);
    }
    $this->assertDatabaseMissing('contributions', [
        'user_id' => $outsider->id,
        'iuran_schedule_id' => $schedule->id,
    ]);
});

test('duplicate iuran schedule for same rt and month is rejected', function () {
    $rt = Rt::factory()->create();
    $ketua = User::factory()->create(['role' => 'rt', 'rt_id' => $rt->id]);

    $payload = ['jenis' => 'Sampah', 'periode' => now()->format('Y-m'), 'amount' => 15000];

    $this->actingAs($ketua)->post(route('iuran_schedules.store'), $payload)->assertRedirect();
    $this->actingAs($ketua)->post(route('iuran_schedules.store'), $payload)->assertSessionHasErrors('periode');

    expect(IuranSchedule::count())->toBe(1);
});

test('warga iuran payment uses locked schedule nominal and ignores tampered amount', function () {
    $rt = Rt::factory()->create();
    $warga = User::factory()->create(['role' => 'warga', 'rt_id' => $rt->id]);

    $schedule = IuranSchedule::create([
        'rt_id' => $rt->id,
        'jenis' => 'Sampah',
        'month' => (int) now()->format('m'),
        'year' => (int) now()->format('Y'),
        'amount' => 15000,
    ]);

    // Warga mencoba mengirim nominal seenaknya — harus dikunci ke jadwal.
    $response = $this->actingAs($warga)->post(route('contributions.store'), [
        'amount' => 1,
        'payment_method' => 'qris',
    ]);

    $response->assertRedirect(route('contributions.index'));
    $this->assertDatabaseHas('contributions', [
        'user_id' => $warga->id,
        'iuran_schedule_id' => $schedule->id,
        'amount' => 15000,
        'payment_method' => 'qris',
        'payment_status' => 'pending',
    ]);
});

test('warga with pending iuran bill is redirected to pay it', function () {
    $rt = Rt::factory()->create();
    $warga = User::factory()->create(['role' => 'warga', 'rt_id' => $rt->id]);

    $bill = Contribution::create([
        'user_id' => $warga->id,
        'payer_name' => $warga->name,
        'amount' => 15000,
        'payment_method' => 'qris',
        'payment_code' => 'IUR-TEST-001',
        'payment_status' => 'pending',
    ]);

    $this->actingAs($warga)->get(route('contributions.create'))
        ->assertRedirect(route('contributions.show', $bill));
});

test('warga can choose payment method when paying iuran bill', function () {
    $warga = User::factory()->create(['role' => 'warga']);

    $bill = Contribution::create([
        'user_id' => $warga->id,
        'payer_name' => $warga->name,
        'amount' => 15000,
        'payment_method' => null,
        'payment_code' => 'IUR-TEST-002',
        'payment_status' => 'pending',
    ]);

    $response = $this->actingAs($warga)->post(route('contributions.pay', $bill), [
        'payment_method' => 'transfer',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('contributions', [
        'id' => $bill->id,
        'payment_method' => 'transfer',
        'payment_status' => 'lunas',
    ]);
    $this->assertNotNull($bill->fresh()->paid_at);
});

test('warga without iuran schedule is told to wait for ketua rt', function () {
    $rt = Rt::factory()->create();
    $warga = User::factory()->create(['role' => 'warga', 'rt_id' => $rt->id]);

    $this->actingAs($warga)->get(route('contributions.create'))
        ->assertRedirect(route('contributions.index'))
        ->assertSessionHas('info');
});

test('same month different jenis is allowed', function () {
    $rt = Rt::factory()->create();
    $ketua = User::factory()->create(['role' => 'rt', 'rt_id' => $rt->id]);

    $this->actingAs($ketua)->post(route('iuran_schedules.store'), [
        'jenis' => 'Sampah',
        'periode' => now()->format('Y-m'),
        'amount' => 15000,
    ])->assertRedirect();

    $this->actingAs($ketua)->post(route('iuran_schedules.store'), [
        'jenis' => 'Keamanan',
        'periode' => now()->format('Y-m'),
        'amount' => 10000,
    ])->assertRedirect();

    expect(IuranSchedule::count())->toBe(2);
});

test('warga cannot access iuran schedules', function () {
    $warga = User::factory()->create(['role' => 'warga']);

    $this->actingAs($warga)->get(route('iuran_schedules.index'))->assertStatus(403);
    $this->actingAs($warga)->get(route('iuran_schedules.create'))->assertStatus(403);
});
