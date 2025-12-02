<?php

use App\Models\Poll;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('displays a poll', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalOrganization()->create();
    $poll = Poll::factory()->for($user->currentOrganization)->create();

    $response = actingAs($user)->get("/poll/{$poll->id}");

    $response->assertSuccessful();
});
