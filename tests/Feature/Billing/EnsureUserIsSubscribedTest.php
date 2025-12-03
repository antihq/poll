<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('redirects unsubscribed users to the subscription-required page when accessing protected routes', function () {
    $user = User::factory()->withPersonalTeam()->create([
        'email_verified_at' => now(),
    ]);

    expect($user->currentTeam->subscribed())->toBeFalse();

    actingAs($user)
        ->get('/dashboard')
        ->assertRedirect('/subscription-required');
});
