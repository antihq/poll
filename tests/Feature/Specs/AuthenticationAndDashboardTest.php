<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('redirects unsubscribed users to the subscription-required page when accessing protected routes', function () {
    $user = User::factory()->withPersonalOrganization()->create([
        'email_verified_at' => now(),
    ]);

    expect($user->currentOrganization->subscribed())->toBeFalse();

    actingAs($user)
        ->get('/dashboard')
        ->assertRedirect(route('subscription-required'));
});

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

it('allows OTP login without email verification requirement', function () {
    // This test verifies that OTP authentication works independently
    // of the old email verification system

    $user = User::factory()->unverified()->create();

    // Simulate OTP verification
    $otp = $user->createOneTimePassword()->password;
    $result = $user->attemptLoginUsingOneTimePassword($otp);

    expect($result->isOk())->toBeTrue();

    // Email verification is now handled during registration via OTP
    // For existing users, OTP login works regardless of verification status
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

it('allows a user to accept an organization invitation and join', function () {
    $organization = \App\Models\Organization::factory()->create();
    $invitedUser = User::factory()->create();
    $invitation = \App\Models\OrganizationInvitation::factory()
        ->for($organization)
        ->create([
            'email' => $invitedUser->email,
        ]);

    $signedUrl = url()->signedRoute('organizations.invitations.accept', $invitation);
    $response = actingAs($invitedUser)->get($signedUrl);

    expect($organization->members()->where('user_id', $invitedUser->id)->exists())->toBeTrue();
    expect($invitedUser->refresh()->currentOrganization->is($organization))->toBeTrue();
    expect(\App\Models\OrganizationInvitation::find($invitation->id))->toBeNull();
    $response->assertRedirect(route('dashboard'));
});

it('returns a successful response from the home page', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
