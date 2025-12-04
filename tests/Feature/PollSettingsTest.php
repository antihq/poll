<?php

use App\Models\Poll;
use App\Models\Team;
use App\Models\User;
use Livewire\Livewire;

it('displays poll settings page', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $team->subscriptions()->create([
        'type' => 'default',
        'stripe_status' => 'active',
        'stripe_id' => 'sub_test_'.uniqid(),
    ]);
    $poll = Poll::factory()->create(['team_id' => $team->id]);

    $response = $this->actingAs($user)->get("/polls/{$poll->id}/settings");

    $response->assertSuccessful();
    $response->assertSee('Poll Settings');
    $response->assertSee($poll->name);
});

it('updates poll settings', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $team->subscriptions()->create([
        'type' => 'default',
        'stripe_status' => 'active',
        'stripe_id' => 'sub_test_'.uniqid(),
    ]);
    $poll = Poll::factory()->create(['team_id' => $team->id]);

    Livewire::actingAs($user)->test('pages::polls.settings', ['poll' => $poll])
        ->set('accepts_responses', false)
        ->set('require_email', true)
        ->set('auto_submit', true)
        ->set('collect_feedback', true)
        ->set('thank_you_message', 'Custom thank you message')
        ->set('thank_you_button_label', 'Continue')
        ->set('thank_you_button_url', 'https://example.com')
        ->set('redirect_url', 'https://redirect.com')
        ->set('hide_branding', true)
        ->call('save');

    $poll->refresh();

    expect($poll->accepts_responses)->toBe(0);
    expect($poll->require_email)->toBe(1);
    expect($poll->auto_submit)->toBe(1);
    expect($poll->collect_feedback)->toBe(1);
    expect($poll->thank_you_message)->toBe('Custom thank you message');
    expect($poll->thank_you_button_label)->toBe('Continue');
    expect($poll->thank_you_button_url)->toBe('https://example.com');
    expect($poll->redirect_url)->toBe('https://redirect.com');
    expect($poll->hide_branding)->toBe(1);
});

it('shows success message after saving settings', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $team->subscriptions()->create([
        'type' => 'default',
        'stripe_status' => 'active',
        'stripe_id' => 'sub_test_'.uniqid(),
    ]);
    $poll = Poll::factory()->create(['team_id' => $team->id]);

    Livewire::actingAs($user)->test('pages::polls.settings', ['poll' => $poll])
        ->set('accepts_responses', false)
        ->call('save')
        ->assertSee('Poll settings updated successfully!');
});
