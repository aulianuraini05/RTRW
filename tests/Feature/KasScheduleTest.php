<?php

use App\Models\CashTransaction;
use App\Models\KasSchedule;
use App\Models\Rt;
use App\Models\User;

test('ketua rt can set monthly kas and bills are auto generated for own rt', function () {
    $rt = Rt::factory()->create();
    $otherRt = Rt::factory()->create();

    $ketua = User::factory()->create(['role' => 'rt', 'rt_id' => $rt->id]);
    $warga1 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt->id]);
    $warga2 = User::factory()->create(['role' => 'warga', 'rt_id' => $rt->id]);
    $outsider = User::factory()->create(['role' => 'warga', 'rt_id' => $otherRt->id]);

    $periode = now()->format('Y-m');

    $response = $this->actingAs($ketua)->post(route('kas_schedules.store'), [
        'periode' => $periode,
        'amount' => 25000,
    ]);

    $schedule = KasSchedule::first();
    expect($schedule)->not->toBeNull();
    expect((int) $schedule->rt_id)->toBe((int) $rt->id);
    expect((float) $schedule->amount)->toBe(25000.0);

    $response->assertRedirect(route('kas_schedules.show', $schedule));

    // Tagihan otomatis untuk warga RT sendiri saja.
    foreach ([$warga1, $warga2] as $warga) {
        $this->assertDatabaseHas('cash_transactions', [
            'user_id' => $warga->id,
            'kas_schedule_id' => $schedule->id,
            'amount' => 25000,
            'payment_status' => 'pending',
        ]);
    }
    $this->assertDatabaseMissing('cash_transactions', [
        'user_id' => $outsider->id,
        'kas_schedule_id' => $schedule->id,
    ]);
});

test('ketua rt cannot create schedule for another rt', function () {
    $rt = Rt::factory()->create();
    $otherRt = Rt::factory()->create();

    $ketua = User::factory()->create(['role' => 'rt', 'rt_id' => $rt->id]);

    // Mencoba memaksa rt_id RT lain — harus tetap masuk ke RT sendiri.
    $this->actingAs($ketua)->post(route('kas_schedules.store'), [
        'rt_id' => $otherRt->id,
        'periode' => now()->format('Y-m'),
        'amount' => 30000,
    ]);

    $schedule = KasSchedule::first();
    expect((int) $schedule->rt_id)->toBe((int) $rt->id);
    expect(KasSchedule::where('rt_id', $otherRt->id)->exists())->toBeFalse();
});

test('duplicate schedule for same rt and month is rejected', function () {
    $rt = Rt::factory()->create();
    $ketua = User::factory()->create(['role' => 'rt', 'rt_id' => $rt->id]);

    $payload = ['periode' => now()->format('Y-m'), 'amount' => 25000];

    $this->actingAs($ketua)->post(route('kas_schedules.store'), $payload)->assertRedirect();
    $this->actingAs($ketua)->post(route('kas_schedules.store'), $payload)->assertSessionHasErrors('periode');

    expect(KasSchedule::count())->toBe(1);
});

test('warga payment uses locked schedule nominal and ignores tampered amount', function () {
    $rt = Rt::factory()->create();
    $warga = User::factory()->create(['role' => 'warga', 'rt_id' => $rt->id]);

    $schedule = KasSchedule::create([
        'rt_id' => $rt->id,
        'month' => (int) now()->format('m'),
        'year' => (int) now()->format('Y'),
        'amount' => 25000,
    ]);

    // Warga mencoba mengirim nominal seenaknya — harus dikunci ke jadwal.
    $response = $this->actingAs($warga)->post(route('cash_transactions.store'), [
        'amount' => 1,
        'payment_method' => 'qris',
    ]);

    $response->assertRedirect(route('cash_transactions.index'));
    $this->assertDatabaseHas('cash_transactions', [
        'user_id' => $warga->id,
        'kas_schedule_id' => $schedule->id,
        'amount' => 25000,
        'payment_method' => 'qris',
        'payment_status' => 'pending',
    ]);
});

test('warga with pending bill is redirected to pay it', function () {
    $rt = Rt::factory()->create();
    $warga = User::factory()->create(['role' => 'warga', 'rt_id' => $rt->id]);

    $bill = CashTransaction::create([
        'user_id' => $warga->id,
        'payer_name' => $warga->name,
        'amount' => 25000,
        'payment_method' => 'qris',
        'payment_code' => 'KAS-TEST-001',
        'payment_status' => 'pending',
    ]);

    $this->actingAs($warga)->get(route('cash_transactions.create'))
        ->assertRedirect(route('cash_transactions.show', $bill));
});

test('warga can choose payment method when paying bill', function () {
    $warga = User::factory()->create(['role' => 'warga']);

    $bill = CashTransaction::create([
        'user_id' => $warga->id,
        'payer_name' => $warga->name,
        'amount' => 25000,
        'payment_method' => null,
        'payment_code' => 'KAS-TEST-002',
        'payment_status' => 'pending',
    ]);

    $response = $this->actingAs($warga)->post(route('cash_transactions.pay', $bill), [
        'payment_method' => 'transfer',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('cash_transactions', [
        'id' => $bill->id,
        'payment_method' => 'transfer',
        'payment_status' => 'lunas',
    ]);
    $this->assertNotNull($bill->fresh()->paid_at);
});

test('warga without schedule is told to wait for ketua rt', function () {
    $rt = Rt::factory()->create();
    $warga = User::factory()->create(['role' => 'warga', 'rt_id' => $rt->id]);

    $this->actingAs($warga)->get(route('cash_transactions.create'))
        ->assertRedirect(route('cash_transactions.index'))
        ->assertSessionHas('info');
});

test('sync generates bills for newly registered warga', function () {
    $rt = Rt::factory()->create();
    $ketua = User::factory()->create(['role' => 'rt', 'rt_id' => $rt->id]);
    User::factory()->create(['role' => 'warga', 'rt_id' => $rt->id]);

    $this->actingAs($ketua)->post(route('kas_schedules.store'), [
        'periode' => now()->format('Y-m'),
        'amount' => 20000,
    ]);

    $schedule = KasSchedule::first();
    expect($schedule->transactions()->count())->toBe(1);

    $newWarga = User::factory()->create(['role' => 'warga', 'rt_id' => $rt->id]);

    $this->actingAs($ketua)->post(route('kas_schedules.sync', $schedule))->assertRedirect();

    $this->assertDatabaseHas('cash_transactions', [
        'user_id' => $newWarga->id,
        'kas_schedule_id' => $schedule->id,
        'payment_status' => 'pending',
    ]);
    expect($schedule->transactions()->count())->toBe(2);
});

test('warga cannot access kas schedules', function () {
    $warga = User::factory()->create(['role' => 'warga']);

    $this->actingAs($warga)->get(route('kas_schedules.index'))->assertStatus(403);
    $this->actingAs($warga)->get(route('kas_schedules.create'))->assertStatus(403);
});
