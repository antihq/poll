<?php

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\OneTimePasswords\Notifications\OneTimePasswordNotification;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\get;

it('renders the login screen', function () {
    $response = get('/login');

    $response->assertStatus(200);
});

it('sends OTP when user enters valid email', function () {
    $user = User::factory()->create();

    Notification::fake();

    $response = Livewire::test('pages::auth.login')
        ->set('email', $user->email)
        ->call('sendOtp');

    $response
        ->assertHasNoErrors()
        ->assertSet('showOtpForm', true);

    Notification::assertSentTo($user, OneTimePasswordNotification::class);
});

it('shows OTP form after sending email', function () {
    $user = User::factory()->create();

    Notification::fake();

    Livewire::test('pages::auth.login')
        ->set('email', $user->email)
        ->call('sendOtp')
        ->assertSee('One-time password');
});

it('authenticates users with valid OTP', function () {
    $user = User::factory()->create();

    // Generate OTP manually for testing
    $otp = $user->createOneTimePassword()->password;

    $response = Livewire::test('pages::auth.login')
        ->set('email', $user->email)
        ->set('one_time_password', $otp)
        ->set('showOtpForm', true)
        ->call('login');

    $response
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    assertAuthenticated();
});

it('rejects authentication with an invalid OTP', function () {
    $user = User::factory()->create();

    $response = Livewire::test('pages::auth.login')
        ->set('email', $user->email)
        ->set('one_time_password', '123456')
        ->set('showOtpForm', true)
        ->call('login');

    $response->assertHasErrors('one_time_password');

    assertGuest();
});

it('does not reveal if user exists when sending OTP', function () {
    $response = Livewire::test('pages::auth.login')
        ->set('email', 'nonexistent@example.com')
        ->call('sendOtp');

    // Should still show OTP form for security
    $response->assertSet('showOtpForm', true);
});

it('logs out authenticated users', function () {
    /** @var User $user */
    $user = User::factory()->create();

    $response = actingAs($user)->post('/logout');

    $response->assertRedirect('/');

    assertGuest();
});

it('renders the registration screen', function () {
    $response = get('/register');

    $response->assertStatus(200);
});

it('creates a user and sends OTP with valid data', function () {
    Notification::fake();

    $response = Livewire::test('pages::auth.register')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->call('sendOtp');

    $response
        ->assertHasNoErrors()
        ->assertSet('showOtpForm', true);

    $user = User::where('email', 'test@example.com')->first();
    expect($user)->not->toBeNull();

    Notification::assertSentTo($user, \Spatie\OneTimePasswords\Notifications\OneTimePasswordNotification::class);
});

it('completes registration with valid OTP', function () {
    Notification::fake();

    // First step: create user
    $livewire = Livewire::test('pages::auth.register')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->call('sendOtp');

    $user = User::where('email', 'test@example.com')->first();
    $otp = $user->createOneTimePassword()->password;

    // Second step: verify OTP
    $response = $livewire
        ->set('one_time_password', $otp)
        ->set('showOtpForm', true)
        ->call('register');

    $response
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    assertAuthenticated();
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

it('creates a personal organization for the new user on registration', function () {
    Notification::fake();

    $userName = 'Test User';
    $userEmail = 'test2@example.com';

    // First step: create user
    $livewire = Livewire::test('pages::auth.register')
        ->set('name', $userName)
        ->set('email', $userEmail)
        ->call('sendOtp');

    $user = User::where('email', $userEmail)->first();
    $otp = $user->createOneTimePassword()->password;

    // Second step: verify OTP
    $livewire
        ->set('one_time_password', $otp)
        ->set('showOtpForm', true)
        ->call('register');

    $organization = \App\Models\Organization::first();

    expect($organization)->not->toBeNull();
    expect($organization->user->is($user))->toBeTrue();
    expect($organization->personal)->toBeTrue();
    expect($user->fresh()->currentOrganization->is($organization))->toBeTrue();
});

it('rejects registration with invalid OTP', function () {
    Notification::fake();

    // First step: create user
    $livewire = Livewire::test('pages::auth.register')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->call('sendOtp');

    $response = $livewire
        ->set('one_time_password', '123456')
        ->set('showOtpForm', true)
        ->call('register');

    $response->assertHasErrors('one_time_password');
});

it('validates unique email during registration', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $response = Livewire::test('pages::auth.register')
        ->set('name', 'Test User')
        ->set('email', 'existing@example.com')
        ->call('sendOtp');

    $response->assertHasErrors('email');
});
