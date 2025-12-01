<?php

// Reference: poll-creation-management.md
// This file tests the poll creation interface and functionality

use App\Models\Poll;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('redirects guests to login when accessing poll creation page', function () {
    $response = get('/polls/create');
    $response->assertRedirect('/login');
});

it('allows authenticated users to access poll creation page', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();

    actingAs($user)
        ->get('/polls/create')
        ->assertOk();
});

it('creates a poll with minimum required data', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('question', 'What is your favorite color?')
        ->set('answers', [
            ['text' => 'Red', 'emoji' => '🔴'],
            ['text' => 'Blue', 'emoji' => '🔵'],
        ])
        ->call('createPoll')
        ->assertHasNoErrors()
        ->assertRedirect('/polls')
        ->assertSessionHas('success', 'Poll created successfully!');

    $poll = Poll::first();
    expect($poll)->not->toBeNull();
    expect($poll->question)->toBe('What is your favorite color?');
    expect($poll->organization_id)->toBe($user->currentOrganization->id);
    expect($poll->status)->toBe('draft');
    expect($poll->layout_type)->toBe('vertical'); // default
    expect($poll->auto_submit)->toBeTrue(); // default
    expect($poll->require_email)->toBeFalse(); // default
    expect($poll->collect_feedback)->toBeFalse(); // default
    expect($poll->hide_branding)->toBeFalse(); // default

    $options = $poll->options()->orderBy('sort_order')->get();
    expect($options)->toHaveCount(2);
    expect($options[0]->answer_text)->toBe('Red');
    expect($options[0]->answer_emoji)->toBe('🔴');
    expect($options[0]->sort_order)->toBe(0);
    expect($options[1]->answer_text)->toBe('Blue');
    expect($options[1]->answer_emoji)->toBe('🔵');
    expect($options[1]->sort_order)->toBe(1);
});

it('creates a poll with all configuration options', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('question', 'How satisfied are you with our service?')
        ->set('answers', [
            ['text' => 'Very Satisfied', 'emoji' => '😊', 'redirect_url' => 'https://example.com/thank-you'],
            ['text' => 'Neutral', 'emoji' => '😐', 'collect_feedback' => true],
            ['text' => 'Dissatisfied', 'emoji' => '😞', 'redirect_url' => 'https://example.com/feedback'],
        ])
        ->set('layout', 'horizontal')
        ->set('autoSubmit', false)
        ->set('requireEmail', true)
        ->set('collectFeedback', true)
        ->set('thankYouMessage', '# Thank you for your feedback!\n\nWe appreciate your time.')
        ->set('hideBranding', true)
        ->call('createPoll')
        ->assertHasNoErrors();

    $poll = Poll::first();
    expect($poll)->not->toBeNull();
    expect($poll->question)->toBe('How satisfied are you with our service?');
    expect($poll->organization_id)->toBe($user->currentOrganization->id);
    expect($poll->status)->toBe('draft');
    expect($poll->layout_type)->toBe('horizontal');
    expect($poll->auto_submit)->toBeFalse();
    expect($poll->require_email)->toBeTrue();
    expect($poll->collect_feedback)->toBeTrue();
    expect($poll->thank_you_message)->toBe('# Thank you for your feedback!\n\nWe appreciate your time.');
    expect($poll->hide_branding)->toBeTrue();

    $options = $poll->options()->orderBy('sort_order')->get();
    expect($options)->toHaveCount(3);
    expect($options[0]->answer_text)->toBe('Very Satisfied');
    expect($options[0]->answer_emoji)->toBe('😊');
    expect($options[0]->redirect_url)->toBe('https://example.com/thank-you');
    expect($options[0]->collect_feedback)->toBeFalse();
    expect($options[0]->sort_order)->toBe(0);

    expect($options[1]->answer_text)->toBe('Neutral');
    expect($options[1]->answer_emoji)->toBe('😐');
    expect($options[1]->redirect_url)->toBeNull();
    expect($options[1]->collect_feedback)->toBeTrue();
    expect($options[1]->sort_order)->toBe(1);

    expect($options[2]->answer_text)->toBe('Dissatisfied');
    expect($options[2]->answer_emoji)->toBe('😞');
    expect($options[2]->redirect_url)->toBe('https://example.com/feedback');
    expect($options[2]->collect_feedback)->toBeFalse();
    expect($options[2]->sort_order)->toBe(2);
});

it('allows adding answer options dynamically', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();

    $component = Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('question', 'Test question?')
        ->set('answers', [
            ['text' => 'Option 1', 'emoji' => ''],
        ]);

    $component->call('addAnswer');

    expect($component->get('answers'))->toHaveCount(2);
    expect($component->get('answers')[1])->toBe(['text' => '', 'emoji' => '']);
});

it('allows removing answer options dynamically', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();

    $component = Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('question', 'Test question?')
        ->set('answers', [
            ['text' => 'Option 1', 'emoji' => ''],
            ['text' => 'Option 2', 'emoji' => ''],
            ['text' => 'Option 3', 'emoji' => ''],
        ]);

    $component->call('removeAnswer', 1);

    expect($component->get('answers'))->toHaveCount(2);
    expect($component->get('answers')[0]['text'])->toBe('Option 1');
    expect($component->get('answers')[1]['text'])->toBe('Option 3');
});

it('prevents poll creation for unsubscribed users', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    actingAs($user)
        ->get('/polls/create')
        ->assertRedirect('/subscription-required');
});
