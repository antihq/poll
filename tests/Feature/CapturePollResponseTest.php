<?php

use App\Models\Answer;
use App\Models\Poll;
use App\Models\PollResponse;
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
        ->set('answer', $answer->ulid)
        ->call('submit');

    $component->assertHasNoErrors();
    $component->assertSee('Thank you for your response!');
});

it('shows validation error when no answer is selected', function () {
    $poll = Poll::factory()->withAnswers(2, ['Yes', 'No'])->create();

    $component = Livewire::test('pages::p.show', ['poll' => $poll])
        ->call('submit');

    $component->assertHasErrors(['answer' => 'required']);
});

it('shows validation error when selected answer does not belong to poll', function () {
    $poll = Poll::factory()->withAnswers(2, ['Yes', 'No'])->create();
    $otherPoll = Poll::factory()->withAnswers(2, ['Maybe', 'Later'])->create();
    $otherAnswer = $otherPoll->answers->first();

    $component = Livewire::test('pages::p.show', ['poll' => $poll])
        ->set('answer', $otherAnswer->ulid)
        ->call('submit');

    $component->assertHasErrors(['answer' => 'exists']);
});

it('shows thank you message after submission and hides form', function () {
    $poll = Poll::factory()->withAnswers(2, ['Yes', 'No'])->create();
    $answer = $poll->answers->first();

    $component = Livewire::test('pages::p.show', ['poll' => $poll])
        ->set('answer', $answer->ulid)
        ->call('submit');

    $component->assertSee('Thank you for your response!');
});

it('preselects answer from URL parameter', function () {
    $poll = Poll::factory()->has(Answer::factory()->count(3))->create();
    $answer = $poll->answers->first();

    $response = get("/p/{$poll->ulid}?answer={$answer->ulid}");

    $response->assertSuccessful();
    $response->assertSee('value="'.$answer->ulid.'"', false);
    $response->assertSee('checked', false);
});

it('stores email in poll response when submitted', function () {
    $poll = Poll::factory()->has(Answer::factory()->count(3))->create();
    $answer = $poll->answers->first();
    $email = 'test@example.com';

    Livewire::test('pages::p.show', ['poll' => $poll])
        ->set('answer', $answer->ulid)
        ->set('email', $email)
        ->call('submit');

    $pollResponse = PollResponse::first();

    expect($pollResponse->poll_id)->toBe($poll->id);
    expect($pollResponse->answer_id)->toBe($answer->id);
    expect($pollResponse->email)->toBe($email);
});

it('stores feedback in poll response when answer has feedback field', function () {
    $poll = Poll::factory()->create();
    $answer = Answer::factory()->for($poll)->create(['feedback_field_label' => 'Please provide feedback']);
    $feedback = 'This is my feedback';

    Livewire::test('pages::p.show', ['poll' => $poll])
        ->set('answer', $answer->ulid)
        ->set('feedback', $feedback)
        ->call('submit');

    $pollResponse = PollResponse::first();

    expect($pollResponse->poll_id)->toBe($poll->id);
    expect($pollResponse->answer_id)->toBe($answer->id);
    expect($pollResponse->feedback)->toBe($feedback);
});

it('requires feedback when answer has feedback field label', function () {
    $poll = Poll::factory()->create();
    $answer = Answer::factory()->for($poll)->create(['feedback_field_label' => 'Please provide feedback']);

    $component = Livewire::test('pages::p.show', ['poll' => $poll])
        ->set('answer', $answer->ulid)
        ->call('submit');

    $component->assertHasErrors(['feedback' => 'required']);
});

it('does not require feedback when answer has no feedback field label', function () {
    $poll = Poll::factory()->create();
    $answer = Answer::factory()->for($poll)->create(['feedback_field_label' => null]);

    $component = Livewire::test('pages::p.show', ['poll' => $poll])
        ->set('answer', $answer->ulid)
        ->call('submit');

    $component->assertHasNoErrors();
});

it('redirects to answer redirect URL when answer has redirect URL', function () {
    $poll = Poll::factory()->create();
    $redirectUrl = 'https://example.com/success';
    $answer = Answer::factory()->for($poll)->create(['redirect_url' => $redirectUrl]);

    $component = Livewire::test('pages::p.show', ['poll' => $poll])
        ->set('answer', $answer->ulid)
        ->call('submit');

    $component->assertRedirect($redirectUrl);
});

it('shows thank you message when answer has no redirect URL', function () {
    $poll = Poll::factory()->create();
    $answer = Answer::factory()->for($poll)->create(['redirect_url' => null]);

    $component = Livewire::test('pages::p.show', ['poll' => $poll])
        ->set('answer', $answer->ulid)
        ->call('submit');

    $component->assertSee('Thank you for your response!');
    $component->assertSet('submitted', true);
});
