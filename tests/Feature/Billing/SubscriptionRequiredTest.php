<?php

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('redirects to dashboard when switching to a subscribed team', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeam()->create();

    $subscribedTeam = Team::factory()->for($user)->withSubscription()->create();

    $unsubscribedTeam = Team::factory()->for($user)->create();
    $user->switchTeam($unsubscribedTeam);

    Livewire::actingAs($user)->test('pages::billing.subscription-required')
        ->call('switchTeam', $subscribedTeam)
        ->assertRedirect('/dashboard');
});

it('stays on the page when switching to a non-subscribed team', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeam()->create();

    $team1 = Team::factory()->for($user)->create();
    $team2 = Team::factory()->for($user)->create();

    $user->switchTeam($team1);

    Livewire::actingAs($user)->test('pages::billing.subscription-required')
        ->call('switchTeam', $team2)
        ->assertOk();
});

it('allows a user to switch to a team they are a member of', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeam()->create();

    $team = Team::factory()->create();
    $team->addMember($user);

    $user->switchTeam($user->teams->first());

    Livewire::actingAs($user)->test('pages::billing.subscription-required')
        ->call('switchTeam', $team)
        ->assertOk();
});

it('prevents a user from switching to a team they neither own nor are a member of', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeam()->create();

    $otherUser = User::factory()->withPersonalTeam()->create();
    $otherTeam = $otherUser->teams->first();

    $user->switchTeam($user->teams->first());

    Livewire::actingAs($user)->test('pages::billing.subscription-required')
        ->call('switchTeam', $otherTeam)
        ->assertForbidden();
});
