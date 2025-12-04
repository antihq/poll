<?php

use App\Models\Answer;
use App\Models\Poll;
use App\Models\PollResponse;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('user can view answer details with responses', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentTeam)->create();
    $answer = Answer::factory()->create(['poll_id' => $poll->id]);

    // Create some responses
    PollResponse::factory()->create([
        'poll_id' => $poll->id,
        'answer_id' => $answer->id,
        'email' => 'test@example.com',
    ]);

    PollResponse::factory()->create([
        'poll_id' => $poll->id,
        'answer_id' => $answer->id,
        'email' => null,
    ]);

    $response = actingAs($user)->get("/polls/{$poll->id}/answers/{$answer->id}");

    $response->assertSuccessful();
    $response->assertSee($poll->title);
    $response->assertSee($answer->text);
    $response->assertSee('test@example.com');
    $response->assertSee('No email provided');
    $response->assertSee('2 responses');
});

test('user sees empty state when answer has no responses', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentTeam)->create();
    $answer = Answer::factory()->create(['poll_id' => $poll->id]);

    $response = actingAs($user)->get("/polls/{$poll->id}/answers/{$answer->id}");

    $response->assertSuccessful();
    $response->assertSee($poll->title);
    $response->assertSee($answer->text);
    $response->assertSee('No responses yet for this answer.');
});

test('unauthenticated user cannot view answer details', function () {
    $poll = Poll::factory()->create();
    $answer = Answer::factory()->create(['poll_id' => $poll->id]);

    $response = $this->get("/polls/{$poll->id}/answers/{$answer->id}");

    $response->assertRedirect('/login');
});
