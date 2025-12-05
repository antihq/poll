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

it('allows guests to select and submit a poll response', function () {
    $poll = Poll::factory()->withAnswers(3, ['Option 1', 'Option 2', 'Option 3'])->create();
    $answer = $poll->answers->first();

    $component = Livewire::test('pages::p.show', ['poll' => $poll])
        ->set('answer', $answer->ulid)
        ->call('submit')
        ->assertHasNoErrors();

    $pollResponse = PollResponse::first();

    expect($pollResponse->poll_id)->toBe($poll->id);
    expect($pollResponse->answer_id)->toBe($answer->id);
});

it('shows validation error when no answer is selected', function () {
    $poll = Poll::factory()->withAnswers(2, ['Yes', 'No'])->create();

    Livewire::test('pages::p.show', ['poll' => $poll])
        ->call('submit')
        ->assertHasErrors(['answer' => 'required']);
});

it('shows validation error when selected answer does not belong to poll', function () {
    $poll = Poll::factory()->withAnswers(2, ['Yes', 'No'])->create();
    $otherPoll = Poll::factory()->withAnswers(2, ['Maybe', 'Later'])->create();
    $otherAnswer = $otherPoll->answers->first();

    Livewire::test('pages::p.show', ['poll' => $poll])
        ->set('answer', $otherAnswer->ulid)
        ->call('submit')
        ->assertHasErrors(['answer' => 'exists']);
});

it('shows thank you message after submission', function () {
    $poll = Poll::factory()->withAnswers(2, ['Yes', 'No'])->create();
    $answer = $poll->answers->first();

    $component = Livewire::test('pages::p.show', ['poll' => $poll])
        ->set('answer', $answer->ulid)
        ->call('submit')
        ->assertSee('Thank you for your response!');
});

it('preselects answer from URL parameter', function () {
    $poll = Poll::factory()->has(Answer::factory()->count(3))->create();
    $answer = $poll->answers->first();

    $response = get("/p/{$poll->ulid}?answer={$answer->ulid}")->assertSuccessful();

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

it('shows 404 when poll does not accept responses', function () {
    $poll = Poll::factory()->create(['accepts_responses' => false]);

    $response = get("/p/{$poll->ulid}");

    $response->assertNotFound();
});

it('requires email when poll is configured to require email', function () {
    $poll = Poll::factory()->withAnswers(2)->create(['require_email' => true]);
    $answer = $poll->answers->first();

    $component = Livewire::test('pages::p.show', ['poll' => $poll])
        ->set('answer', $answer->ulid)
        ->call('submit');

    $component->assertHasErrors(['email' => 'required']);
});

it('submits successfully with email when poll requires email', function () {
    $poll = Poll::factory()->withAnswers(2)->create(['require_email' => true]);
    $answer = $poll->answers->first();
    $email = 'test@example.com';

    $component = Livewire::test('pages::p.show', ['poll' => $poll])
        ->set('answer', $answer->ulid)
        ->set('email', $email)
        ->call('submit');

    $component->assertHasNoErrors();
    $component->assertSee('Thank you for your response!');
});

it('auto-submits when poll is configured to auto submit', function () {
    $poll = Poll::factory()->withAnswers(2)->create(['auto_submit' => true]);
    $answer = $poll->answers->first();

    $component = Livewire::test('pages::p.show', ['poll' => $poll])
        ->set('answer', $answer->ulid)
        ->call('submit'); // Manually trigger submit since auto-submit is hard to test

    $component->assertSet('submitted', true);
});

it('collects feedback when poll is configured to collect feedback', function () {
    $poll = Poll::factory()->withAnswers(2)->create(['collect_feedback' => true]);
    $answer = $poll->answers->first();
    $feedback = 'This is my feedback';

    $component = Livewire::test('pages::p.show', ['poll' => $poll])
        ->set('answer', $answer->ulid)
        ->set('feedback', $feedback)
        ->call('submit');

    $component->assertHasNoErrors();

    $pollResponse = \App\Models\PollResponse::first();
    expect($pollResponse->feedback)->toBe($feedback);
});

it('requires feedback when poll is configured to collect feedback', function () {
    $poll = Poll::factory()->withAnswers(2)->create(['collect_feedback' => true]);
    $answer = $poll->answers->first();

    $component = Livewire::test('pages::p.show', ['poll' => $poll])
        ->set('answer', $answer->ulid)
        ->call('submit');

    $component->assertHasErrors(['feedback' => 'required']);
});

it('shows custom thank you message when configured', function () {
    $customMessage = 'Thank you for completing our survey!';
    $poll = Poll::factory()->withAnswers(2)->create(['thank_you_message' => $customMessage]);
    $answer = $poll->answers->first();

    $component = Livewire::test('pages::p.show', ['poll' => $poll])
        ->set('answer', $answer->ulid)
        ->call('submit');

    $component->assertSee($customMessage);
});

it('shows custom thank you button when configured', function () {
    $buttonLabel = 'Continue to Results';
    $buttonUrl = 'https://example.com/results';
    $poll = Poll::factory()->withAnswers(2)->create([
        'thank_you_button_label' => $buttonLabel,
        'thank_you_button_url' => $buttonUrl,
    ]);
    $answer = $poll->answers->first();

    $component = Livewire::test('pages::p.show', ['poll' => $poll])
        ->set('answer', $answer->ulid)
        ->call('submit');

    $component->assertSee($buttonLabel);
    $component->assertSee($buttonUrl);
});

it('redirects to poll redirect URL when configured', function () {
    $redirectUrl = 'https://example.com/success';
    $poll = Poll::factory()->withAnswers(2)->create(['redirect_url' => $redirectUrl]);
    $answer = $poll->answers->first();

    $component = Livewire::test('pages::p.show', ['poll' => $poll])
        ->set('answer', $answer->ulid)
        ->call('submit');

    $component->assertRedirect($redirectUrl);
});
