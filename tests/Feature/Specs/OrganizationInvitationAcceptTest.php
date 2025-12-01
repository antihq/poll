<?php

use App\Models\Organization;
use App\Models\OrganizationInvitation;
use App\Models\User;

it('allows a user to accept an organization invitation and join', function () {
    $organization = Organization::factory()->create();
    $invitedUser = User::factory()->create();
    $invitation = OrganizationInvitation::factory()
        ->for($organization)
        ->create([
            'email' => $invitedUser->email,
        ]);

    $signedUrl = url()->signedRoute('organizations.invitations.accept', $invitation);
    $response = $this->actingAs($invitedUser)->get($signedUrl);

    expect($organization->members()->where('user_id', $invitedUser->id)->exists())->toBeTrue();
    expect($invitedUser->refresh()->currentOrganization->is($organization))->toBeTrue();
    expect(OrganizationInvitation::find($invitation->id))->toBeNull();
    $response->assertRedirect(route('dashboard'));
});
