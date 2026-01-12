<?php

use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

it('redirects guests from profile to login', function () {
    $response = get('/profile');
    $response->assertRedirect('/login');
});

it('allows authenticated user to view profile page', function () {
    $user = User::factory()->create([
        'name' => 'Nama Test',
    ]);

    actingAs($user);

    $response = get('/profile');

    $response->assertOk();
    $response->assertSeeText('Nama Test');
    $response->assertSeeText($user->email);
});

it('shows username in header when authenticated', function () {
    $user = User::factory()->create(['name' => 'Header User']);
    actingAs($user);
    get('/dashboard')->assertOk()->assertSeeText('Header User');
});

it('logout works and redirects to login', function () {
    $user = User::factory()->create();
    actingAs($user);

    // GET a protected page first to initialize session and CSRF token
    $this->get('/profile');
    $token = session('_token');

    $response = $this->post('/logout', ['_token' => $token]);
    $response->assertRedirect('/login');
});
