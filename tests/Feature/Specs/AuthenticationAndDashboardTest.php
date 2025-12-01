<?php

// specs/authentication-system.md - OTP Authentication & Dashboard Access

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

// specs/billing-system.md - Subscription Middleware - Test Cases
it('redirects unsubscribed users to the subscription-required page when accessing protected routes', function () {
    $user = User::factory()->withPersonalOrganization()->create([
        'email_verified_at' => now(),
    ]);

    expect($user->currentOrganization->subscribed())->toBeFalse();

    actingAs($user)
        ->get('/dashboard')
        ->assertRedirect(route('subscription-required'));
});

// specs/test-specification.md - Dashboard Tests - Test Cases
it('redirects guests to the login page', function () {
    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');
});

// specs/test-specification.md - Dashboard Tests - Test Cases
it('authenticated users can visit the dashboard', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $this->actingAs($user);

    $response = $this->get('/dashboard');
    $response->assertStatus(200);
});

// specs/authentication-system.md - Email Verification System - Test Cases
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

// specs/organization-management.md - Organization Invitations - Acceptance Process
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

// specs/test-specification.md - Example Tests - Test Cases
it('returns a successful response from the home page', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
