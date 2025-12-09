<?php

use App\Models\Poll;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('redirects unsubscribed users to the subscription-required page when accessing poll creation at limit', function () {
    $user = User::factory()->withPersonalTeam()->create([
        'email_verified_at' => now(),
    ]);

    // Create 1000 polls to reach the limit
    Poll::factory()->count(1000)->create([
        'team_id' => $user->currentTeam->id,
    ]);

    expect($user->currentTeam->subscribed())->toBeFalse();
    expect($user->currentTeam->pollCount())->toBe(1000);

    // Test that Livewire component handles limit correctly
    Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('name', 'Test Poll')
        ->set('question', 'Test question?')
        ->set('answers', ['Answer 1', 'Answer 2'])
        ->call('create')
        ->assertRedirect('/subscription-required');
});

it('allows unsubscribed users to access poll creation when under limit', function () {
    $user = User::factory()->withPersonalTeam()->create([
        'email_verified_at' => now(),
    ]);

    expect($user->currentTeam->subscribed())->toBeFalse();
    expect($user->currentTeam->pollCount())->toBe(0);

    Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->assertOk();
});

it('allows unsubscribed users to access dashboard regardless of poll count', function () {
    $user = User::factory()->withPersonalTeam()->create([
        'email_verified_at' => now(),
    ]);

    // Create 1000 polls to reach the limit
    Poll::factory()->count(1000)->create([
        'team_id' => $user->currentTeam->id,
    ]);

    expect($user->currentTeam->subscribed())->toBeFalse();
    expect($user->currentTeam->pollCount())->toBe(1000);

    actingAs($user)
        ->get('/dashboard')
        ->assertOk();
});
