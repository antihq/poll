<?php

use App\Models\Answer;
use App\Models\Poll;
use App\Models\PollResponse;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('allows a user to view answer details', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentTeam)->create();
    $answer = Answer::factory()->for($poll)->create();
    PollResponse::factory()->count(2)->for($poll)->for($answer)->create();

    actingAs($user)->get("/polls/{$poll->id}/answers/{$answer->id}")->assertSuccessful();
});
