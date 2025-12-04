<?php

use App\Models\User;
use Livewire\Livewire;

it('renders the registration screen', function () {
    $response = $this->get('/register');

    $response->assertOk();
    $response->assertSee('Create an account');
});

it('creates a user and sends OTP with valid data', function () {
    Livewire::test('pages::auth.register')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->call('sendOtp')
        ->assertHasNoErrors()
        ->assertSet('showOtpForm', true);

    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);
});

it('completes registration with valid OTP', function () {
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $otp = $user->createOneTimePassword()->password;

    Livewire::test('pages::auth.register')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('one_time_password', $otp)
        ->call('register')
        ->assertRedirect('/dashboard');
});

it('creates a personal team for the new user on registration', function () {
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $otp = $user->createOneTimePassword()->password;

    Livewire::test('pages::auth.register')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('one_time_password', $otp)
        ->call('register');

    $this->assertDatabaseHas('teams', [
        'user_id' => $user->id,
        'name' => 'Test User',
        'personal' => true,
    ]);

    $user->refresh();
    expect($user->currentTeam)->not->toBeNull();
    expect($user->currentTeam->personal)->toBeTrue();
});

it('rejects registration with invalid OTP', function () {
    User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    Livewire::test('pages::auth.register')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('one_time_password', 'invalid')
        ->call('register')
        ->assertHasErrors(['one_time_password']);
});

it('validates unique email during registration', function () {
    User::factory()->create(['email' => 'test@example.com']);

    Livewire::test('pages::auth.register')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->call('sendOtp')
        ->assertHasErrors(['email']);
});
