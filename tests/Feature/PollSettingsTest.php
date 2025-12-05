<?php

use App\Models\Poll;
use App\Models\User;
use Livewire\Livewire;

it('updates poll settings with thank you message', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $poll = Poll::factory()->for($user->currentTeam)->create();

    Livewire::actingAs($user)->test('pages::polls.settings', ['poll' => $poll])
        ->set('accepts_responses', false)
        ->set('require_email', true)
        ->set('auto_submit', true)
        ->set('collect_feedback', true)
        ->set('submission_action', 'message')
        ->set('thank_you_message', 'Custom thank you message')
        ->set('thank_you_button_label', 'Continue')
        ->set('thank_you_button_url', 'https://example.com')
        ->set('hide_branding', true)
        ->set('layout', 'horizontal')
        ->call('save')
        ->assertHasNoErrors();

    $poll->refresh();

    expect($poll->accepts_responses)->toBeFalse();
    expect($poll->require_email)->toBeTrue();
    expect($poll->auto_submit)->toBeTrue();
    expect($poll->collect_feedback)->toBeTrue();
    expect($poll->thank_you_message)->toBe('Custom thank you message');
    expect($poll->thank_you_button_label)->toBe('Continue');
    expect($poll->thank_you_button_url)->toBe('https://example.com');
    expect($poll->redirect_url)->toBeNull();
    expect($poll->submissionAction())->toBe('message');
    expect($poll->hide_branding)->toBeTrue();
    expect($poll->layout)->toBe('horizontal');
});

it('updates poll settings with redirect URL', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $poll = Poll::factory()->for($user->currentTeam)->create();

    Livewire::actingAs($user)->test('pages::polls.settings', ['poll' => $poll])
        ->set('accepts_responses', false)
        ->set('require_email', true)
        ->set('auto_submit', true)
        ->set('collect_feedback', true)
        ->set('submission_action', 'redirect')
        ->set('redirect_url', 'https://redirect.com')
        ->set('hide_branding', true)
        ->set('layout', 'vertical')
        ->call('save');

    $poll->refresh();

    expect($poll->accepts_responses)->toBeFalse();
    expect($poll->require_email)->toBeTrue();
    expect($poll->auto_submit)->toBeTrue();
    expect($poll->collect_feedback)->toBeTrue();
    expect($poll->thank_you_message)->toBeNull();
    expect($poll->thank_you_button_label)->toBeNull();
    expect($poll->thank_you_button_url)->toBeNull();
    expect($poll->redirect_url)->toBe('https://redirect.com');
    expect($poll->submissionAction())->toBe('redirect');
    expect($poll->hide_branding)->toBeTrue();
    expect($poll->layout)->toBe('vertical');
});

it('validates redirect URL is required when redirect is selected', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $poll = Poll::factory()->for($user->currentTeam)->create();

    Livewire::actingAs($user)->test('pages::polls.settings', ['poll' => $poll])
        ->set('submission_action', 'redirect')
        ->set('redirect_url', '')
        ->call('save')
        ->assertHasErrors(['redirect_url' => 'required']);
});
