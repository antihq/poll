<?php

use App\Models\Poll;
use Livewire\Livewire;

use function Pest\Laravel\get;

it('displays a poll for guests', function () {
    $poll = Poll::factory()->withAnswers(3, ['Yes', 'No', 'Maybe'])->create();

    $response = get("/p/{$poll->ulid}");

    $response->assertSuccessful();
});

it('shows 404 for non-existent poll', function () {
    $response = get('/p/invalid-ulid');

    $response->assertNotFound();
});

it('allows guests to select and submit a poll response', function () {
    $poll = Poll::factory()->withAnswers(3, ['Option 1', 'Option 2', 'Option 3'])->create();
    $answer = $poll->answers->first();

    $component = Livewire::test('pages::p.show', ['poll' => $poll])
        ->set('selectedAnswer', $answer->id)
        ->call('submit');

    $component->assertHasNoErrors();
    $component->assertSee('Thank you for your response!');
});

it('shows validation error when no answer is selected', function () {
    $poll = Poll::factory()->withAnswers(2, ['Yes', 'No'])->create();

    $component = Livewire::test('pages::p.show', ['poll' => $poll])
        ->call('submit');

    $component->assertHasErrors(['selectedAnswer' => 'required']);
});

it('shows validation error when selected answer does not belong to the poll', function () {
    $poll = Poll::factory()->withAnswers(2, ['Yes', 'No'])->create();
    $otherPoll = Poll::factory()->withAnswers(2, ['Maybe', 'Later'])->create();
    $otherAnswer = $otherPoll->answers->first();

    $component = Livewire::test('pages::p.show', ['poll' => $poll])
        ->set('selectedAnswer', $otherAnswer->id)
        ->call('submit');

    $component->assertHasErrors(['selectedAnswer' => 'exists']);
});

it('shows thank you message after submission and hides the form', function () {
    $poll = Poll::factory()->withAnswers(2, ['Yes', 'No'])->create();
    $answer = $poll->answers->first();

    $component = Livewire::test('pages::p.show', ['poll' => $poll])
        ->set('selectedAnswer', $answer->id)
        ->call('submit');

    $component->assertSee('Thank you for your response!');
});
