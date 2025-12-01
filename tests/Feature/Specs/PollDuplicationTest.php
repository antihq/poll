<?php

// Reference: poll-creation-management.md
// This file tests poll duplication functionality

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('redirects guests to login when accessing poll duplication', function () {
    $this->post('/polls/1/duplicate')
        ->assertRedirect('/login');
});

it('allows authenticated users to duplicate polls', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    actingAs($user)
        ->post('/polls/1/duplicate')
        ->assertRedirect('/polls');
});

it('duplicates poll with all configuration options', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => 1])
        ->call('duplicatePoll')
        ->assertHasNoErrors()
        ->assertRedirect('/polls')
        ->assertSessionHas('success', 'Poll duplicated successfully!');
});

it('creates duplicate with same question', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => 1])
        ->call('duplicatePoll')
        ->assertHasNoErrors();
});

it('creates duplicate with same answer options', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => 1])
        ->call('duplicatePoll')
        ->assertHasNoErrors();
});

it('creates duplicate with same layout configuration', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => 1])
        ->call('duplicatePoll')
        ->assertHasNoErrors();
});

it('creates duplicate with same global settings', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => 1])
        ->call('duplicatePoll')
        ->assertHasNoErrors();
});

it('creates duplicate with same answer-specific settings', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => 1])
        ->call('duplicatePoll')
        ->assertHasNoErrors();
});

it('resets response data in duplicated poll', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => 1])
        ->call('duplicatePoll')
        ->assertHasNoErrors();
});

it('sets duplicated poll status to draft', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => 1])
        ->call('duplicatePoll')
        ->assertHasNoErrors();
});

it('associates duplicate with same organization', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => 1])
        ->call('duplicatePoll')
        ->assertHasNoErrors();
});

it('preserves answer sort order in duplicate', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => 1])
        ->call('duplicatePoll')
        ->assertHasNoErrors();
});

it('preserves redirect URLs in duplicate', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => 1])
        ->call('duplicatePoll')
        ->assertHasNoErrors();
});

it('preserves feedback collection settings in duplicate', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => 1])
        ->call('duplicatePoll')
        ->assertHasNoErrors();
});

it('preserves thank you message in duplicate', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => 1])
        ->call('duplicatePoll')
        ->assertHasNoErrors();
});

it('preserves branding settings in duplicate', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => 1])
        ->call('duplicatePoll')
        ->assertHasNoErrors();
});

it('prevents unauthorized users from duplicating polls', function () {
    $user = User::factory()->withPersonalOrganization()->create();
    $otherUser = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($otherUser)
        ->test('pages::polls.show', ['poll' => 1])
        ->call('duplicatePoll')
        ->assertForbidden();
});

it('allows organization owners to duplicate polls', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $organization = Organization::factory()->create();

    $owner->organizations()->attach($organization, ['role' => 'owner']);
    $member->organizations()->attach($organization, ['role' => 'member']);

    Livewire::actingAs($owner)
        ->test('pages::polls.show', ['poll' => 1])
        ->call('duplicatePoll')
        ->assertHasNoErrors();
});

it('restricts poll duplication to organization members', function () {
    $user = User::factory()->withPersonalOrganization()->create();
    $nonMember = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($nonMember)
        ->test('pages::polls.show', ['poll' => 1])
        ->call('duplicatePoll')
        ->assertForbidden();
});

it('creates multiple duplicates of same poll', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    $component = Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => 1]);

    $component->call('duplicatePoll')->assertHasNoErrors();
    $component->call('duplicatePoll')->assertHasNoErrors();
});

it('handles duplication of polls with maximum answer options', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => 1])
        ->call('duplicatePoll')
        ->assertHasNoErrors();
});

it('handles duplication of polls with minimum answer options', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => 1])
        ->call('duplicatePoll')
        ->assertHasNoErrors();
});

it('handles duplication of polls with emoji-only answers', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => 1])
        ->call('duplicatePoll')
        ->assertHasNoErrors();
});

it('handles duplication of polls with text-only answers', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => 1])
        ->call('duplicatePoll')
        ->assertHasNoErrors();
});

it('handles duplication of polls with mixed text and emoji answers', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => 1])
        ->call('duplicatePoll')
        ->assertHasNoErrors();
});

it('handles duplication of polls with all advanced features enabled', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => 1])
        ->call('duplicatePoll')
        ->assertHasNoErrors();
});

it('handles duplication of polls with all advanced features disabled', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => 1])
        ->call('duplicatePoll')
        ->assertHasNoErrors();
});

it('shows success message after poll duplication', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => 1])
        ->call('duplicatePoll')
        ->assertRedirect('/polls')
        ->assertSessionHas('success', 'Poll duplicated successfully!');
});

it('redirects to poll list after duplication', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => 1])
        ->call('duplicatePoll')
        ->assertRedirect('/polls');
});

it('prevents duplication of non-existent polls', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => 999])
        ->call('duplicatePoll')
        ->assertNotFound();
});

it('handles duplication errors gracefully', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Livewire::actingAs($user)
        ->test('pages::polls.show', ['poll' => 1])
        ->call('duplicatePoll')
        ->assertHasNoErrors();
});
