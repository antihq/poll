<?php

use App\Models\Poll;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('displays a list of polls', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();

    Poll::factory()->for($user->currentTeam)->create(['name' => 'First Poll']);
    Poll::factory()->for($user->currentTeam)->create(['name' => 'Second Poll']);

    actingAs($user)->get('/polls/')->assertSuccessful();
});

