<?php

// Reference: poll-creation-management.md
// This file contains ALL validation tests for poll creation and editing
// Validation tests are centralized here to avoid duplication across other test files

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('validates question is required during poll creation', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('question', '')
        ->set('answers', [
            ['text' => 'Option 1', 'emoji' => ''],
            ['text' => 'Option 2', 'emoji' => ''],
        ])
        ->call('createPoll')
        ->assertHasErrors(['question' => 'required']);
});

it('validates question maximum length during poll creation', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('question', str_repeat('a', 256))
        ->set('answers', [
            ['text' => 'Option 1', 'emoji' => ''],
            ['text' => 'Option 2', 'emoji' => ''],
        ])
        ->call('createPoll')
        ->assertHasErrors(['question' => 'max']);
});

it('validates minimum 2 answer options during poll creation', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('question', 'Test question?')
        ->set('answers', [
            ['text' => 'Only one option', 'emoji' => ''],
        ])
        ->call('createPoll')
        ->assertHasErrors(['answers' => 'min']);
});

it('validates maximum 10 answer options during poll creation', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    $answers = [];
    for ($i = 1; $i <= 11; $i++) {
        $answers[] = ['text' => "Option {$i}", 'emoji' => ''];
    }

    Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('question', 'Test question?')
        ->set('answers', $answers)
        ->call('createPoll')
        ->assertHasErrors(['answers' => 'max']);
});

it('validates answer text is required when no emoji provided', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('question', 'Test question?')
        ->set('answers', [
            ['text' => '', 'emoji' => ''],
            ['text' => 'Valid option', 'emoji' => ''],
        ])
        ->call('createPoll')
        ->assertHasErrors(['answers.0.text' => 'required_without:answers.0.emoji']);
});

it('validates answer text maximum length', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('question', 'Test question?')
        ->set('answers', [
            ['text' => str_repeat('a', 256), 'emoji' => ''],
            ['text' => 'Valid option', 'emoji' => ''],
        ])
        ->call('createPoll')
        ->assertHasErrors(['answers.0.text' => 'max']);
});

it('validates redirect URLs are valid URLs', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('question', 'Test question?')
        ->set('answers', [
            ['text' => 'Option 1', 'emoji' => '', 'redirect_url' => 'invalid-url'],
            ['text' => 'Option 2', 'emoji' => ''],
        ])
        ->call('createPoll')
        ->assertHasErrors(['answers.0.redirect_url' => 'url']);
});

it('validates redirect URL maximum length', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('question', 'Test question?')
        ->set('answers', [
            ['text' => 'Option 1', 'emoji' => '', 'redirect_url' => 'https://example.com/'.str_repeat('a', 1000)],
            ['text' => 'Option 2', 'emoji' => ''],
        ])
        ->call('createPoll')
        ->assertHasErrors(['answers.0.redirect_url' => 'max']);
});

it('validates thank you message maximum length', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('question', 'Test question?')
        ->set('answers', [
            ['text' => 'Option 1', 'emoji' => ''],
            ['text' => 'Option 2', 'emoji' => ''],
        ])
        ->set('thankYouMessage', str_repeat('a', 1001))
        ->call('createPoll')
        ->assertHasErrors(['thankYouMessage' => 'max']);
});

it('validates layout type is one of allowed values', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('question', 'Test question?')
        ->set('answers', [
            ['text' => 'Option 1', 'emoji' => ''],
            ['text' => 'Option 2', 'emoji' => ''],
        ])
        ->set('layout', 'invalid-layout')
        ->call('createPoll')
        ->assertHasErrors(['layout' => 'in']);
});

it('validates auto submit is boolean', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('question', 'Test question?')
        ->set('answers', [
            ['text' => 'Option 1', 'emoji' => ''],
            ['text' => 'Option 2', 'emoji' => ''],
        ])
        ->set('autoSubmit', 'invalid-boolean')
        ->call('createPoll')
        ->assertHasErrors(['autoSubmit' => 'boolean']);
});

it('validates require email is boolean', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('question', 'Test question?')
        ->set('answers', [
            ['text' => 'Option 1', 'emoji' => ''],
            ['text' => 'Option 2', 'emoji' => ''],
        ])
        ->set('requireEmail', 'invalid-boolean')
        ->call('createPoll')
        ->assertHasErrors(['requireEmail' => 'boolean']);
});

it('validates collect feedback is boolean', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('question', 'Test question?')
        ->set('answers', [
            ['text' => 'Option 1', 'emoji' => ''],
            ['text' => 'Option 2', 'emoji' => ''],
        ])
        ->set('collectFeedback', 'invalid-boolean')
        ->call('createPoll')
        ->assertHasErrors(['collectFeedback' => 'boolean']);
});

it('validates hide branding is boolean', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('question', 'Test question?')
        ->set('answers', [
            ['text' => 'Option 1', 'emoji' => ''],
            ['text' => 'Option 2', 'emoji' => ''],
        ])
        ->set('hideBranding', 'invalid-boolean')
        ->call('createPoll')
        ->assertHasErrors(['hideBranding' => 'boolean']);
});

it('validates answer-specific collect feedback is boolean', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('question', 'Test question?')
        ->set('answers', [
            ['text' => 'Option 1', 'emoji' => '', 'collect_feedback' => 'invalid-boolean'],
            ['text' => 'Option 2', 'emoji' => ''],
        ])
        ->call('createPoll')
        ->assertHasErrors(['answers.0.collect_feedback' => 'boolean']);
});

it('validates real-time during poll creation', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('question', '')
        ->assertHasErrors(['question' => 'required']);
});

it('validates real-time answer options during poll creation', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('answers', [
            ['text' => '', 'emoji' => ''],
            ['text' => 'Valid option', 'emoji' => ''],
        ])
        ->assertHasErrors(['answers.0.text' => 'required_without:answers.0.emoji']);
});

it('validates real-time redirect URLs during poll creation', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('answers', [
            ['text' => 'Option 1', 'emoji' => '', 'redirect_url' => 'invalid-url'],
            ['text' => 'Option 2', 'emoji' => ''],
        ])
        ->assertHasErrors(['answers.0.redirect_url' => 'url']);
});

it('validates question is required during poll editing', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => 1])
        ->set('question', '')
        ->call('updatePoll')
        ->assertHasErrors(['question' => 'required']);
});

it('validates minimum answer options during poll editing', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => 1])
        ->set('answers', [
            ['text' => 'Only option', 'emoji' => ''],
        ])
        ->call('updatePoll')
        ->assertHasErrors(['answers' => 'min']);
});

it('validates maximum answer options during poll editing', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    $answers = [];
    for ($i = 1; $i <= 11; $i++) {
        $answers[] = ['text' => "Option {$i}", 'emoji' => ''];
    }

    Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => 1])
        ->set('answers', $answers)
        ->call('updatePoll')
        ->assertHasErrors(['answers' => 'max']);
});

it('validates real-time during poll editing', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => 1])
        ->set('question', '')
        ->assertHasErrors(['question' => 'required']);
});

it('validates real-time answer options during poll editing', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => 1])
        ->set('answers', [
            ['text' => '', 'emoji' => ''],
            ['text' => 'Valid option', 'emoji' => ''],
        ])
        ->assertHasErrors(['answers.0.text' => 'required_without:answers.0.emoji']);
});

it('validates real-time redirect URLs during poll editing', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => 1])
        ->set('answers', [
            ['text' => 'Option 1', 'emoji' => '', 'redirect_url' => 'invalid-url'],
            ['text' => 'Option 2', 'emoji' => ''],
        ])
        ->assertHasErrors(['answers.0.redirect_url' => 'url']);
});

it('allows valid poll data to pass validation', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('question', 'What is your favorite color?')
        ->set('answers', [
            ['text' => 'Red', 'emoji' => '🔴', 'redirect_url' => 'https://example.com/red'],
            ['text' => 'Blue', 'emoji' => '🔵', 'collect_feedback' => true],
        ])
        ->set('layout', 'vertical')
        ->set('autoSubmit', true)
        ->set('requireEmail', false)
        ->set('collectFeedback', false)
        ->set('thankYouMessage', 'Thank you for voting!')
        ->set('hideBranding', false)
        ->call('createPoll')
        ->assertHasNoErrors();
});

it('allows valid poll data to pass validation during editing', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => 1])
        ->set('question', 'Updated question?')
        ->set('answers', [
            ['text' => 'Updated Option 1', 'emoji' => '🎉', 'redirect_url' => 'https://example.com/updated'],
            ['text' => 'Updated Option 2', 'emoji' => '⭐'],
        ])
        ->set('layout', 'horizontal')
        ->set('autoSubmit', false)
        ->set('requireEmail', true)
        ->set('collectFeedback', true)
        ->set('thankYouMessage', 'Updated thank you message!')
        ->set('hideBranding', true)
        ->call('updatePoll')
        ->assertHasNoErrors();
});

it('validates emoji-only answers are allowed', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('question', 'Test question?')
        ->set('answers', [
            ['text' => '', 'emoji' => '👍'],
            ['text' => 'Text option', 'emoji' => ''],
        ])
        ->call('createPoll')
        ->assertHasNoErrors();
});

it('validates text-only answers are allowed', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('question', 'Test question?')
        ->set('answers', [
            ['text' => 'Text only option', 'emoji' => ''],
            ['text' => 'Another text option', 'emoji' => ''],
        ])
        ->call('createPoll')
        ->assertHasNoErrors();
});

it('validates text and emoji answers are allowed', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('question', 'Test question?')
        ->set('answers', [
            ['text' => 'Option with emoji', 'emoji' => '🎉'],
            ['text' => 'Another option', 'emoji' => '⭐'],
        ])
        ->call('createPoll')
        ->assertHasNoErrors();
});

it('validates empty answers without text or emoji are rejected', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('question', 'Test question?')
        ->set('answers', [
            ['text' => '', 'emoji' => ''],
            ['text' => 'Valid option', 'emoji' => ''],
        ])
        ->call('createPoll')
        ->assertHasErrors(['answers.0.text' => 'required_without:answers.0.emoji']);
});

it('validates multiple empty answers are rejected', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.create')
        ->set('question', 'Test question?')
        ->set('answers', [
            ['text' => '', 'emoji' => ''],
            ['text' => '', 'emoji' => ''],
            ['text' => 'Valid option', 'emoji' => ''],
        ])
        ->call('createPoll')
        ->assertHasErrors([
            'answers.0.text' => 'required_without:answers.0.emoji',
            'answers.1.text' => 'required_without:answers.1.emoji',
        ]);
});
