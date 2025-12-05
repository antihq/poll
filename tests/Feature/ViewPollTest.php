<?php

use App\Models\Answer;
use App\Models\Poll;
use App\Models\PollResponse;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('displays a poll', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentTeam)->create();

    $response = actingAs($user)->get("/polls/{$poll->id}");

    $response->assertSuccessful();
});

it('can delete a poll', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentTeam)->create();
    $answer = Answer::factory()->for($poll)->create();
    $response = PollResponse::factory()->for($poll)->for($answer)->create();

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => $poll])
        ->call('delete');

    expect($poll->fresh())->toBeNull();
    expect($answer->fresh())->toBeNull();
    expect($response->fresh())->toBeNull();
});
