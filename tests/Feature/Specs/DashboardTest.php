<?php

// specs/authentication-system.md - Dashboard Access & Navigation

use App\Models\User;

it('redirects guests to the login page', function () {
    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');
});

it('authenticated users can visit the dashboard', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $this->actingAs($user);

    $response = $this->get('/dashboard');
    $response->assertStatus(200);
});

it('returns a successful response from the home page', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
