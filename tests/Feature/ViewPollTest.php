<?php

use App\Models\Poll;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('displays a poll', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentOrganization)->create();

    $response = actingAs($user)->get("/polls/{$poll->id}");

    $response->assertSuccessful();
});
