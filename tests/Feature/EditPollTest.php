<?php

use App\Models\Answer;
use App\Models\Poll;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('displays the poll edit page', function () {
    $user = User::factory()->withPersonalTeamAndSubscription()->create();

    $poll = Poll::factory()->for($user->currentTeam)->create(['name' => 'Test Poll', 'question' => 'Test Question?']);
    Answer::factory()->for($poll)->create(['text' => 'Answer 1', 'sort_order' => 0]);
    Answer::factory()->for($poll)->create(['text' => 'Answer 2', 'sort_order' => 1]);

    actingAs($user)->get("/polls/{$poll->id}/edit")->assertSuccessful();

    $component = Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => $poll]);

    $component->assertStatus(200);
    $component->assertSee('Edit poll');
    $component->assertSet('name', 'Test Poll');
    $component->assertSet('question', 'Test Question?');
    $component->assertSet('answers', ['Answer 1', 'Answer 2']);
});

it('updates a poll with valid data', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $poll = Poll::factory()->for($user->currentTeam)->create();
    $answer1 = Answer::factory()->for($poll)->create(['text' => 'Answer 1', 'sort_order' => 0]);
    $answer2 = Answer::factory()->for($poll)->create(['text' => 'Answer 2', 'sort_order' => 1]);

    Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => $poll])
        ->set('name', 'Updated Poll Name')
        ->set('question', 'Updated question?')
        ->set('answers', ['Updated Answer 1', 'Updated Answer 2'])
        ->call('update');

    $poll->refresh();
    expect($poll->name)->toBe('Updated Poll Name');
    expect($poll->question)->toBe('Updated question?');

    $answer1->refresh();
    expect($answer1->text)->toBe('Updated Answer 1');
    expect($answer1->sort_order)->toBe(0);

    $answer2->refresh();
    expect($answer2->text)->toBe('Updated Answer 2');
    expect($answer2->sort_order)->toBe(1);
});

it('can add new answers when editing', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $poll = Poll::factory()->for($user->currentTeam)->create();
    Answer::factory()->for($poll)->create(['text' => 'Answer 1', 'sort_order' => 0]);
    Answer::factory()->for($poll)->create(['text' => 'Answer 2', 'sort_order' => 1]);

    Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => $poll])
        ->set('name', $poll->name)
        ->set('question', $poll->question)
        ->set('answers', ['Answer 1', 'Answer 2', 'New Answer 3'])
        ->call('update');

    $newAnswer = Answer::where('poll_id', $poll->id)
        ->where('text', 'New Answer 3')
        ->first();
    expect($newAnswer)->not->toBeNull();
    expect($newAnswer->sort_order)->toBe(2);
});

it('can remove answers when editing', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $poll = Poll::factory()->for($user->currentTeam)->create();
    $answer1 = Answer::factory()->for($poll)->create(['text' => 'Answer 1', 'sort_order' => 0]);
    $answer2 = Answer::factory()->for($poll)->create(['text' => 'Answer 2', 'sort_order' => 1]);
    $answer3 = Answer::factory()->for($poll)->create(['text' => 'Answer 3', 'sort_order' => 2]);

    Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => $poll])
        ->set('name', $poll->name)
        ->set('question', $poll->question)
        ->set('answers', ['Answer 1', 'Answer 2'])
        ->call('update');

    expect(Answer::find($answer3->id))->toBeNull();
});

it('shows validation errors when less than two answers are provided', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $poll = Poll::factory()->for($user->currentTeam)->create();
    Answer::factory()->for($poll)->create(['text' => 'Answer 1', 'sort_order' => 0]);
    Answer::factory()->for($poll)->create(['text' => 'Answer 2', 'sort_order' => 1]);

    Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => $poll])
        ->set('name', $poll->name)
        ->set('question', $poll->question)
        ->set('answers', ['Answer 1'])
        ->call('update')
        ->assertHasErrors(['answers']);
});

it('can sort answers by dragging and dropping', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $poll = Poll::factory()->for($user->currentTeam)->create();
    Answer::factory()->for($poll)->create(['text' => 'Answer 1', 'sort_order' => 0]);
    Answer::factory()->for($poll)->create(['text' => 'Answer 2', 'sort_order' => 1]);
    Answer::factory()->for($poll)->create(['text' => 'Answer 3', 'sort_order' => 2]);

    $component = Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => $poll]);

    $component->call('sortAnswer', 0, 2);

    $component->assertSet('answers', ['Answer 2', 'Answer 3', 'Answer 1']);
});
