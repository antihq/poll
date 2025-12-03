<?php

use App\Models\Poll;
use App\Models\PollResponse;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('displays a list of polls for the current team', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();

    $poll1 = Poll::factory()->for($user->currentTeam)->create(['name' => 'First Poll']);
    $poll2 = Poll::factory()->for($user->currentTeam)->create(['name' => 'Second Poll']);
    PollResponse::factory()->count(3)->for($poll1)->create();
    PollResponse::factory()->count(5)->for($poll2)->create();

    $response = actingAs($user)->get('/polls/');
    $response->assertSuccessful();
    $response->assertSee('First Poll');
    $response->assertSee('Second Poll');
    $response->assertSee('3');
    $response->assertSee('5');
});

it('only shows polls from the current team', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();

    $poll1 = Poll::factory()->for($user->currentTeam)->create(['name' => 'My Poll']);
    PollResponse::factory()->count(2)->for($poll1)->create();

    $response = actingAs($user)->get('/polls/');
    $response->assertSuccessful();
    $response->assertSee('My Poll');
    $response->assertSee('2');
    $response->assertDontSee('First Poll');
    $response->assertDontSee('Second Poll');
});

it('displays zero responses for polls with no responses', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();

    $poll = Poll::factory()->for($user->currentTeam)->create(['name' => 'Empty Poll']);

    PollResponse::factory()->count(0)->for($poll)->create();

    $response = actingAs($user)->get('/polls/');
    $response->assertSuccessful();
    $response->assertSee('Empty Poll');
    $response->assertSee('0');
});

