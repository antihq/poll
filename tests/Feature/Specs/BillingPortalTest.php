<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('redirects unsubscribed users to the dashboard when accessing the billing portal', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalOrganization()->create();

    $response = actingAs($user)->get('/billing-portal');

    $response->assertRedirect(route('subscription-required'));
});

it('switching to a subscribed organization redirects to dashboard', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalOrganization()->create();

    $subscribedOrg = \App\Models\Organization::factory()->for($user)->withSubscription()->create();

    $unsubscribedOrg = \App\Models\Organization::factory()->for($user)->create();
    $user->switchOrganization($unsubscribedOrg);

    Livewire::actingAs($user)->test('pages::billing.subscription-required')
        ->call('switchOrganization', $subscribedOrg)
        ->assertRedirect(route('dashboard'));
});

it('switching to a non-subscribed organization stays on the page', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalOrganization()->create();

    $org1 = \App\Models\Organization::factory()->for($user)->create();
    $org2 = \App\Models\Organization::factory()->for($user)->create();

    $user->switchOrganization($org1);

    Livewire::actingAs($user)->test('pages::billing.subscription-required')
        ->call('switchOrganization', $org2)
        ->assertOk();
});

it('user can switch to an organization they are a member of', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalOrganization()->create();

    $org = \App\Models\Organization::factory()->create();
    $org->addMember($user);

    $user->switchOrganization($user->organizations->first());

    Livewire::actingAs($user)->test('pages::billing.subscription-required')
        ->call('switchOrganization', $org)
        ->assertOk();
});

it('user cannot switch to an organization they neither own nor are a member of', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalOrganization()->create();

    $otherUser = User::factory()->withPersonalOrganization()->create();
    $otherOrg = $otherUser->organizations->first();

    $user->switchOrganization($user->organizations->first());

    Livewire::actingAs($user)->test('pages::billing.subscription-required')
        ->call('switchOrganization', $otherOrg)
        ->assertForbidden();
});
