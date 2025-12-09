<?php

use App\Models\Poll;
use App\Models\User;
use Livewire\Livewire;

it('creates a new poll for the team with valid data', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();

    $pollData = [
        'name' => 'Team Satisfaction Survey',
        'question' => 'How satisfied are you with our team collaboration?',
        'answers' => [
            'Very satisfied',
            'Satisfied',
            'Neutral',
            'Dissatisfied',
            'Very dissatisfied',
        ],
    ];

    // Test that the Livewire component can be mounted
    Livewire::actingAs($user)->test('pages::polls.create')->assertSuccessful();

    $component = Livewire::actingAs($user)->test('pages::polls.create')
        ->set('name', $pollData['name'])
        ->set('question', $pollData['question'])
        ->set('answers', $pollData['answers'])
        ->call('create')
        ->assertHasNoErrors();

    $poll = Poll::first();

    expect($poll)->not->toBeNull();
    expect($poll->team->is($user->currentTeam))->toBeTrue();
    expect($poll->question)->toBe($pollData['question']);

    $answerTexts = $poll->answers->pluck('text')->toArray();

    expect($answerTexts)->toBe($pollData['answers']);
});

it('shows validation errors when less than two answers are provided', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();

    $component = Livewire::actingAs($user)->test('pages::polls.create')
        ->set('name', 'Test Poll')
        ->set('question', 'Test question?')
        ->set('answers', ['Answer 1'])
        ->call('create')
        ->assertHasErrors(['answers']);

    $component = Livewire::actingAs($user)->test('pages::polls.create')
        ->set('name', 'Test Poll')
        ->set('question', 'Test question?')
        ->set('answers', [])
        ->call('create')
        ->assertHasErrors(['answers']);
});

it('can add and remove answers dynamically', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();

    $component = Livewire::actingAs($user)->test('pages::polls.create')
        ->set('name', 'Dynamic Poll')
        ->set('question', 'Can we add multiple answers?')
        ->set('answers', ['Yes', 'No']);

    // Test adding an answer
    $component->call('addAnswer');
    $component->assertSet('answers', ['Yes', 'No', '']);

    // Test adding another answer
    $component->set('answers.2', 'Maybe')
        ->call('addAnswer');
    $component->assertSet('answers', ['Yes', 'No', 'Maybe', '']);

    // Test removing an answer (should work since we have more than 2)
    $component->call('removeAnswer', 1);
    $component->assertSet('answers', ['Yes', 'Maybe', '']);

    // Test creating poll with dynamic answers
    $component->set('answers.2', 'Not sure')
        ->call('create');

    $component->assertHasNoErrors();
    $component->assertRedirect('/polls/1');

    $poll = Poll::first();

    expect($poll->name)->toBe('Dynamic Poll');
    expect($poll->answers->count())->toBe(3);
    expect($poll->answers->pluck('text')->toArray())->toBe(['Yes', 'Maybe', 'Not sure']);
});

it('can sort answers by dragging and dropping', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();

    $component = Livewire::actingAs($user)->test('pages::polls.create')
        ->set('name', 'Sortable Poll')
        ->set('question', 'Can we sort answers?')
        ->set('answers', ['First', 'Second', 'Third']);

    $component->call('sortAnswer', 0, 2);

    $component->assertSet('answers', ['Second', 'Third', 'First']);

    $component->call('sortAnswer', 1, 0);

    $component->assertSet('answers', ['Third', 'Second', 'First']);

    $component->call('create');

    $component->assertHasNoErrors();

    $poll = Poll::first();

    expect($poll->answers->count())->toBe(3);
    expect($poll->answers->pluck('text')->toArray())->toBe(['Third', 'Second', 'First']);
});

it('prevents poll creation when team reaches free limit and redirects team owner to subscription page', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeam()->create();

    // Get the team directly from the teams relationship
    $team = $user->teams()->first();

    // Create 1000 polls to reach the limit
    Poll::factory()->count(1000)->create([
        'team_id' => $team->id,
    ]);

    $team->refresh();
    expect($team->pollCount())->toBe(1000);
    expect($team->hasReachedFreePollLimit())->toBeTrue();

    $component = Livewire::actingAs($user)->test('pages::polls.create')
        ->set('name', 'Test Poll')
        ->set('question', 'Test question?')
        ->set('answers', ['Answer 1', 'Answer 2'])
        ->call('create')
        ->assertRedirect('/subscription-required');
});

it('prevents poll creation for non-team owners when team reaches free limit', function () {
    /** @var User $owner */
    $owner = User::factory()->withPersonalTeam()->create();

    // Get the team directly from the teams relationship
    $team = $owner->teams()->first();

    /** @var User $member */
    $member = User::factory()->create();
    $team->addMember($member);
    $member->current_team_id = $team->id;
    $member->save();

    // Create 1000 polls to reach the limit
    Poll::factory()->count(1000)->create([
        'team_id' => $team->id,
    ]);

    $team->refresh();
    expect($team->pollCount())->toBe(1000);
    expect($team->hasReachedFreePollLimit())->toBeTrue();

    $component = Livewire::actingAs($member)->test('pages::polls.create')
        ->set('name', 'Test Poll')
        ->set('question', 'Test question?')
        ->set('answers', ['Answer 1', 'Answer 2'])
        ->call('create');

    // Should not redirect, but should not create Poll either
    expect(Poll::count())->toBe(1000); // Should still be 1000, not 1001
});

it('allows poll creation when team is under free limit', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeam()->create();

    // Get the team directly from the teams relationship
    $team = $user->teams()->first();

    // Create 999 polls (under the limit)
    Poll::factory()->count(999)->create([
        'team_id' => $team->id,
    ]);

    $component = Livewire::actingAs($user)->test('pages::polls.create')
        ->set('name', 'Test Poll')
        ->set('question', 'Test question?')
        ->set('answers', ['Answer 1', 'Answer 2'])
        ->call('create')
        ->assertHasNoErrors();

    expect(Poll::count())->toBe(1000); // Now 1000 polls
});

it('allows poll creation for subscribed teams regardless of poll count', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();

    // Get the team directly from the teams relationship
    $team = $user->teams()->first();

    // Create 1000 polls
    Poll::factory()->count(1000)->create([
        'team_id' => $team->id,
    ]);

    $component = Livewire::actingAs($user)->test('pages::polls.create')
        ->set('name', 'Test Poll')
        ->set('question', 'Test question?')
        ->set('answers', ['Answer 1', 'Answer 2'])
        ->call('create')
        ->assertHasNoErrors();

    expect(Poll::count())->toBe(1001); // Should be 1001 since subscribed teams have no limit
});

it('maintains correct poll count after deletion', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeam()->create();

    // Get the team directly from the teams relationship
    $team = $user->teams()->first();

    // Create 5 polls
    Poll::factory()->count(5)->create([
        'team_id' => $team->id,
    ]);

    $team->refresh(); // Refresh to get latest team data
    expect($team->pollCount())->toBe(5);
    expect($team->hasReachedFreePollLimit())->toBeFalse();

    // Delete 2 polls
    Poll::limit(2)->get()->each->delete();

    // Poll count should still be 5 (deleted polls count against quota)
    expect($team->pollCount())->toBe(5);
    expect($team->hasReachedFreePollLimit())->toBeFalse();
});
