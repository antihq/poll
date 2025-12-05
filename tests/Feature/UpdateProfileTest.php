<?php

use App\Models\User;
use Livewire\Livewire;

it('displays the profile page', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $this->actingAs($user);

    $this->get('/settings/profile')->assertOk();
});

it('updates the profile information', function () {
    $user = User::factory()->withPersonalTeam()->create();

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.profile')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toEqual('Test User');
    expect($user->email)->toEqual('test@example.com');
    expect($user->email_verified_at)->toBeNull();
});

it('keeps email verification status unchanged when email address is unchanged', function () {
    $user = User::factory()->withPersonalTeam()->create();

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.profile')
        ->set('name', 'Test User')
        ->set('email', $user->email)
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});
