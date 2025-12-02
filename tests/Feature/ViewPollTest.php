<?php

use App\Models\Poll;

use function Pest\Laravel\get;

it('displays a poll', function () {
    $poll = Poll::factory()->create();

    $response = get("/poll/{$poll->id}");

    $response->assertSuccessful();
});
