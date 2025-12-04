<?php

use App\Models\Answer;
use App\Models\Poll;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('saves settings successfully with livewire component', function () {
    $user = User::factory()->withPersonalTeamAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentTeam)->create();
    $answer = Answer::factory()->for($poll)->create();

    // Test saving redirect URL
    Livewire::actingAs($user)
        ->test('pages::answers.settings', ['answer' => $answer])
        ->set('enable_redirect_url', true)
        ->set('redirect_url', 'https://example.com/thank-you')
        ->set('show_feedback_field', false)
        ->set('feedback_field_label', '')
        ->call('update');

    $answer->refresh();
    expect($answer->redirect_url)->toBe('https://example.com/thank-you');
    expect($answer->feedback_field_label)->toBeNull();

    // Test saving feedback field
    Livewire::actingAs($user)
        ->test('pages::answers.settings', ['answer' => $answer])
        ->set('enable_redirect_url', false)
        ->set('redirect_url', '')
        ->set('show_feedback_field', true)
        ->set('feedback_field_label', 'What did you think?')
        ->call('update');

    $answer->refresh();
    expect($answer->redirect_url)->toBeNull();
    expect($answer->feedback_field_label)->toBe('What did you think?');
});

it('validates required_if rules correctly', function () {
    $user = User::factory()->withPersonalTeamAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentTeam)->create();
    $answer = Answer::factory()->for($poll)->create();

    // Test that redirect URL is required when enabled but empty
    Livewire::actingAs($user)
        ->test('pages::answers.settings', ['answer' => $answer])
        ->set('enable_redirect_url', true)
        ->set('redirect_url', '')
        ->set('show_feedback_field', false)
        ->set('feedback_field_label', '')
        ->call('update')
        ->assertHasErrors('redirect_url');

    // Test that feedback label is required when enabled but empty
    Livewire::actingAs($user)
        ->test('pages::answers.settings', ['answer' => $answer])
        ->set('enable_redirect_url', false)
        ->set('redirect_url', '')
        ->set('show_feedback_field', true)
        ->set('feedback_field_label', '')
        ->call('update')
        ->assertHasErrors('feedback_field_label');
});
