<?php

use App\Models\Poll;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

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

    $team = $user->currentTeam;
    Poll::factory()->for($team)->count(1000)->create();

    $team->refresh();
    expect($team->polls_created)->toBe(1000);
    expect($team->hasReachedFreePollLimit())->toBeTrue();

    actingAs($user)->get('/polls/create')->assertRedirect('/dashboard');
});

it('allows poll creation when team is under free limit', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeam()->create();

    $team = $user->currentTeam;
    Poll::factory()->for($team)->count(999)->create();

    Livewire::actingAs($user)->test('pages::polls.create')
        ->set('name', 'Test Poll')
        ->set('question', 'Test question?')
        ->set('answers', ['Answer 1', 'Answer 2'])
        ->call('create')
        ->assertHasNoErrors();

    $team->refresh();
    expect($team->polls_created)->toBe(1000);
    expect(Poll::count())->toBe(1000);
});

it('allows poll creation for subscribed teams regardless of poll count', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeam()->create();

    $team = $user->currentTeam;
    Poll::factory()->for($team)->count(1000)->create();

    Livewire::actingAs($user)->test('pages::polls.create')
        ->set('name', 'Test Poll')
        ->set('question', 'Test question?')
        ->set('answers', ['Answer 1', 'Answer 2'])
        ->call('create')
        ->assertHasNoErrors();

    $team->refresh();
    expect($team->polls_created)->toBe(1001);
    expect(Poll::count())->toBe(1001);
});

it('maintains correct poll count after deletion', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeam()->create();

    $team = $user->currentTeam;
    Poll::factory()->for($team)->count(5)->create();

    $team->refresh();
    expect($team->polls_created)->toBe(5);
    expect($team->hasReachedFreePollLimit())->toBeFalse();

    Poll::limit(2)->get()->each->delete();

    $team->refresh();
    expect($team->polls_created)->toBe(5);
    expect($team->hasReachedFreePollLimit())->toBeFalse();
});
