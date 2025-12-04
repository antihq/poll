<?php

use App\Models\Poll;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

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

    $response = actingAs($user)->get('/polls/create');

    $response->assertSuccessful();

    $component = Livewire::actingAs($user)->test('pages::polls.create')
        ->set('name', $pollData['name'])
        ->set('question', $pollData['question'])
        ->set('answers', $pollData['answers'])
        ->call('create');

    $component->assertHasNoErrors();

    $poll = Poll::first();
    expect($poll)->not->toBeNull();
    expect($poll->team->is($user->currentTeam))->toBeTrue();
    expect($poll->question)->toBe($pollData['question']);
    $answerTexts = $poll->answers->pluck('text')->toArray();
    expect($answerTexts)->toBe($pollData['answers']);

    $component->assertRedirect('/polls/1');
});

it('shows validation errors when less than two answers are provided', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();

    $component = Livewire::actingAs($user)->test('pages::polls.create')
        ->set('name', 'Test Poll')
        ->set('question', 'Test question?')
        ->set('answers', ['Answer 1'])
        ->call('create');

    $component->assertHasErrors(['answers']);

    $component = Livewire::actingAs($user)->test('pages::polls.create')
        ->set('name', 'Test Poll')
        ->set('question', 'Test question?')
        ->set('answers', [])
        ->call('create');

    $component->assertHasErrors(['answers']);
});

it('redirects guests to login page', function () {
    $response = get('/polls/create');

    $response->assertRedirect('/login');
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
