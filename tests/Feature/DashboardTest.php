<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->withPersonalTeamAndSubscription()->create();

    $response = actingAs($user)->get('/dashboard')->assertSuccessful();
});
