<?php

use App\Models\Rt;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $rt = Rt::factory()->create(['code' => 'RT01']);

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'rt_code' => 'RT01',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $user = auth()->user();
    expect($user->role)->toBe('warga');
    expect($user->rt_id)->toBe($rt->id);
});

test('registration fails with invalid rt code', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'rt_code' => 'RT99',
    ]);

    $response->assertSessionHasErrors('rt_code');
    $this->assertGuest();
});
