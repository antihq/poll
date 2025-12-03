<?php

use App\Models\Poll;
use App\Models\PollResponse;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('displays a list of polls for the current organization', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();

    $poll1 = Poll::factory()->for($user->currentOrganization)->create(['name' => 'First Poll']);
    $poll2 = Poll::factory()->for($user->currentOrganization)->create(['name' => 'Second Poll']);

    PollResponse::factory()->count(3)->for($poll1)->create();
    PollResponse::factory()->count(5)->for($poll2)->create();

    $response = actingAs($user)->get('/polls/');

    $response->assertSuccessful();
    $response->assertSee('First Poll');
    $response->assertSee('Second Poll');
    $response->assertSee('3');
    $response->assertSee('5');
});

it('only shows polls from the current organization', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    /** @var User $otherUser */
    $otherUser = User::factory()->withPersonalOrganizationAndSubscription()->create();

    Poll::factory()->for($user->currentOrganization)->create(['name' => 'My Poll']);
    Poll::factory()->for($otherUser->currentOrganization)->create(['name' => 'Other Poll']);

    $response = actingAs($user)->get('/polls/');

    $response->assertSuccessful();
    $response->assertSee('My Poll');
    $response->assertDontSee('Other Poll');
});

it('displays zero responses for polls with no responses', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();

    Poll::factory()->for($user->currentOrganization)->create(['name' => 'Empty Poll']);

    $response = actingAs($user)->get('/polls/');

    $response->assertSuccessful();
    $response->assertSee('Empty Poll');
    $response->assertSee('0');
});
