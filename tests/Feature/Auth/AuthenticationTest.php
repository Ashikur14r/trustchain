<?php

use App\Models\User;
use App\Services\GoogleAuthenticatorService;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users with two-factor enabled must verify a code after their password', function () {
    $secret = 'JBSWY3DPEHPK3PXP';
    $user = User::factory()->create([
        'two_factor_secret' => $secret,
        'two_factor_enabled' => true,
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertGuest();
    $response->assertRedirect(route('login.2fa'));

    $challenge = $this->post(route('login.2fa.verify'), [
        'code' => GoogleAuthenticatorService::getCode($secret),
    ]);

    $this->assertAuthenticatedAs($user);
    $challenge->assertRedirect(route('dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
