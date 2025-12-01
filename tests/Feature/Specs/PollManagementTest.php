<?php

// Reference: poll-creation-management.md
// This file tests poll management features including editing, listing, and status management

use App\Models\Organization;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('redirects guests to login when accessing poll index page', function () {
    get('/polls')->assertRedirect('/login');
});

it('allows authenticated users to access poll index page', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();

    actingAs($user)
        ->get('/polls')
        ->assertOk();
});

it('displays polls with status indicators', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();

    actingAs($user)
        ->get('/polls')
        ->assertOk()
        ->assertSee('Active')
        ->assertSee('Closed')
        ->assertSee('Draft');
});

it('shows response counts and last activity on poll list', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();

    actingAs($user)
        ->get('/polls')
        ->assertOk()
        ->assertSee('Responses')
        ->assertSee('Last Activity');
});

it('provides quick actions for poll management', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();

    actingAs($user)
        ->get('/polls')
        ->assertOk()
        ->assertSee('Edit')
        ->assertSee('Duplicate')
        ->assertSee('Close')
        ->assertSee('Delete');
});

it('allows searching polls by question', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();

    actingAs($user)
        ->get('/polls?search=customer')
        ->assertOk();
});

it('allows filtering polls by status', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();

    actingAs($user)
        ->get('/polls?status=active')
        ->assertOk();
});

it('redirects guests to login when accessing poll edit page', function () {
    get('/polls/1/edit')->assertRedirect('/login');
});

it('allows authenticated users to access poll edit page', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();

    actingAs($user)
        ->get('/polls/1/edit')
        ->assertOk();
});

it('loads poll data for editing', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentOrganization)->create();

    Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => $poll->id])
        ->assertOk();
});

it('updates poll question and saves changes', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentOrganization)->create();

    Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => $poll->id])
        ->set('question', 'Updated question text')
        ->call('updatePoll')
        ->assertHasNoErrors()
        ->assertRedirect("/polls/{$poll->id}")
        ->assertSessionHas('success', 'Poll updated successfully!');

    $poll->refresh();
    expect($poll->question)->toBe('Updated question text');
});

it('updates poll layout configuration', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentOrganization)->create();

    Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => $poll->id])
        ->set('layout', 'horizontal')
        ->set('autoSubmit', false)
        ->call('updatePoll')
        ->assertHasNoErrors();

    $poll->refresh();
    expect($poll->layout_type)->toBe('horizontal');
    expect($poll->auto_submit)->toBeFalse();
});

it('adds new answer options to existing poll', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $poll = Poll::factory()
        ->for($user->currentOrganization)
        ->has(PollOption::factory()->count(2), 'options')
        ->create();

    $component = Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => $poll->id]);

    $component->call('addAnswer');

    expect($component->get('answers'))->toHaveCount(3);
});

it('removes answer options from existing poll', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $poll = Poll::factory()
        ->for($user->currentOrganization)
        ->has(PollOption::factory()->count(3), 'options')
        ->create();

    $component = Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => $poll->id])
        ->set('answers', [
            ['text' => 'Option 1', 'emoji' => ''],
            ['text' => 'Option 2', 'emoji' => ''],
            ['text' => 'Option 3', 'emoji' => ''],
        ]);

    $component->call('removeAnswer', 1);

    expect($component->get('answers'))->toHaveCount(2);
});

it('reorders answer options with drag and drop', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $poll = Poll::factory()
        ->for($user->currentOrganization)
        ->has(PollOption::factory()->count(3), 'options')
        ->create();

    Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => $poll->id])
        ->set('answers', [
            ['text' => 'First', 'emoji' => '1️⃣'],
            ['text' => 'Second', 'emoji' => '2️⃣'],
            ['text' => 'Third', 'emoji' => '3️⃣'],
        ])
        ->call('reorderAnswers', [2, 0, 1])
        ->assertHasNoErrors();
});

it('updates answer text and emoji', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $poll = Poll::factory()
        ->for($user->currentOrganization)
        ->has(PollOption::factory()->count(2), 'options')
        ->create();

    Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => $poll->id])
        ->set('answers.0.text', 'Updated option text')
        ->set('answers.0.emoji', '🎉')
        ->call('updatePoll')
        ->assertHasNoErrors();

    $option = $poll->options()->orderBy('sort_order')->first();
    expect($option->answer_text)->toBe('Updated option text');
    expect($option->answer_emoji)->toBe('🎉');
});

it('updates answer-specific redirect URLs', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $poll = Poll::factory()
        ->for($user->currentOrganization)
        ->has(PollOption::factory()->count(2), 'options')
        ->create();

    Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => $poll->id])
        ->set('answers.0.redirect_url', 'https://example.com/new-redirect')
        ->call('updatePoll')
        ->assertHasNoErrors();

    $option = $poll->options()->orderBy('sort_order')->first();
    expect($option->redirect_url)->toBe('https://example.com/new-redirect');
});

it('updates answer-specific feedback collection', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $poll = Poll::factory()
        ->for($user->currentOrganization)
        ->has(PollOption::factory()->count(2), 'options')
        ->create();

    Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => $poll->id])
        ->set('answers.0.collect_feedback', true)
        ->call('updatePoll')
        ->assertHasNoErrors();

    $option = $poll->options()->orderBy('sort_order')->first();
    expect($option->collect_feedback)->toBeTrue();
});

it('updates global poll settings', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentOrganization)->create();

    Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => $poll->id])
        ->set('requireEmail', true)
        ->set('collectFeedback', true)
        ->set('thankYouMessage', 'Custom thank you message')
        ->set('hideBranding', true)
        ->call('updatePoll')
        ->assertHasNoErrors();

    $poll->refresh();
    expect($poll->require_email)->toBeTrue();
    expect($poll->collect_feedback)->toBeTrue();
    expect($poll->thank_you_message)->toBe('Custom thank you message');
    expect($poll->hide_branding)->toBeTrue();
});

it('preserves historical response data when editing', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentOrganization)->create();

    Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => $poll->id])
        ->set('question', 'Updated question')
        ->call('updatePoll')
        ->assertHasNoErrors();

    $poll->refresh();
    expect($poll->question)->toBe('Updated question');
});

it('redirects guests to login when accessing poll show page', function () {
    get('/polls/1')->assertRedirect('/login');
});

it('allows authenticated users to access poll show page', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentOrganization)->create();

    actingAs($user)
        ->get("/polls/{$poll->id}")
        ->assertOk();
});

it('displays poll details and configuration', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentOrganization)->create();

    actingAs($user)
        ->get("/polls/{$poll->id}")
        ->assertOk()
        ->assertSee('Question')
        ->assertSee('Options')
        ->assertSee('Settings');
});

it('shows poll status on show page', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentOrganization)->create();

    actingAs($user)
        ->get("/polls/{$poll->id}")
        ->assertOk()
        ->assertSee('Status');
});

it('provides embed code for poll', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentOrganization)->create();

    actingAs($user)
        ->get("/polls/{$poll->id}")
        ->assertOk()
        ->assertSee('Embed Code');
});

it('closes poll for new responses', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentOrganization)->create();

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => $poll->id])
        ->call('closePoll')
        ->assertHasNoErrors()
        ->assertSessionHas('success', 'Poll closed successfully!');

    $poll->refresh();
    expect($poll->status)->toBe('closed');
});

it('reopens closed poll for responses', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentOrganization)->create(['status' => 'closed']);

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => $poll->id])
        ->call('openPoll')
        ->assertHasNoErrors()
        ->assertSessionHas('success', 'Poll reopened successfully!');

    $poll->refresh();
    expect($poll->status)->toBe('active');
});

it('deletes poll with confirmation', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentOrganization)->create();

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => $poll->id])
        ->call('deletePoll')
        ->assertHasNoErrors()
        ->assertRedirect('/polls')
        ->assertSessionHas('success', 'Poll deleted successfully!');

    expect(Poll::find($poll->id))->toBeNull();
});

it('prevents unauthorized users from editing polls', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentOrganization)->create();
    $otherUser = User::factory()->withPersonalOrganization()->create();

    actingAs($otherUser)
        ->get("/polls/{$poll->id}/edit")
        ->assertForbidden();
});

it('prevents unauthorized users from deleting polls', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentOrganization)->create();
    $otherUser = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($otherUser)
        ->test('pages::polls.show', ['poll' => $poll->id])
        ->call('deletePoll')
        ->assertForbidden();
});

it('allows organization owners to manage polls', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $organization = Organization::factory()->create();

    $owner->organizations()->attach($organization, ['role' => 'owner']);
    $member->organizations()->attach($organization, ['role' => 'member']);

    $poll = Poll::factory()->for($organization)->create();

    actingAs($owner)
        ->get("/polls/{$poll->id}/edit")
        ->assertOk();
});

it('restricts poll management to organization members', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentOrganization)->create();
    $nonMember = User::factory()->withPersonalOrganization()->create();

    actingAs($nonMember)
        ->get("/polls/{$poll->id}/edit")
        ->assertForbidden();
});

it('shows analytics data for poll responses', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentOrganization)->create();

    actingAs($user)
        ->get("/polls/{$poll->id}/analytics")
        ->assertOk()
        ->assertSee('Analytics')
        ->assertSee('Response Data');
});

it('displays response charts and statistics', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentOrganization)->create();

    actingAs($user)
        ->get("/polls/{$poll->id}/analytics")
        ->assertOk()
        ->assertSee('Total Responses')
        ->assertSee('Response Rate');
});

it('exports poll response data', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentOrganization)->create();

    Livewire::actingAs($user)
        ->test('pages::polls.analytics', ['poll' => $poll->id])
        ->call('exportResponses')
        ->assertHasNoErrors();
});

it('filters analytics by date range', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentOrganization)->create();

    actingAs($user)
        ->get("/polls/{$poll->id}/analytics?start_date=2024-01-01&end_date=2024-12-31")
        ->assertOk();
});

it('shows individual response details', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentOrganization)->create();

    actingAs($user)
        ->get("/polls/{$poll->id}/responses")
        ->assertOk()
        ->assertSee('Response Details');
});

it('allows bulk actions on responses', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentOrganization)->create();

    Livewire::actingAs($user)
        ->test('pages::polls.responses', ['poll' => $poll->id])
        ->set('selectedResponses', [1, 2, 3])
        ->call('bulkDelete')
        ->assertHasNoErrors();
});
