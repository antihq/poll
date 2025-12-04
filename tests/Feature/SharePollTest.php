<?php

use App\Models\Answer;
use App\Models\Poll;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('displays the poll share page', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentTeam)->has(Answer::factory()->count(3))->create();

    $response = actingAs($user)->get("/polls/{$poll->id}/share");

    $response->assertSuccessful();
});

it('shows poll question and answers on share page', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();
    $poll = Poll::factory()
        ->for($user->currentTeam)
        ->has(Answer::factory()->count(3)->sequence(['text' => 'Yes'], ['text' => 'No'], ['text' => 'Maybe']))
        ->create(['question' => 'Do you like Laravel?']);

    $response = actingAs($user)->get("/polls/{$poll->id}/share");

    $response->assertSee('Do you like Laravel?');
    $response->assertSee('Yes');
    $response->assertSee('No');
    $response->assertSee('Maybe');
});

it('generates correct share links for universal platform', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();
    $poll = Poll::factory()
        ->for($user->currentTeam)
        ->has(Answer::factory()->count(2)->sequence(['text' => 'Option A'], ['text' => 'Option B']))
        ->create(['question' => 'Which option do you prefer?']);

    $component = Livewire::actingAs($user)->test('pages::polls.share', ['poll' => $poll])
        ->set('platform', 'universal');

    $shareContent = $component->get('shareContent');

    expect($shareContent)->toContain('Which option do you prefer?');
    expect($shareContent)->toContain('Option A');
    expect($shareContent)->toContain('Option B');
    expect($shareContent)->toContain('href="'.url("/p/{$poll->ulid}?answer=").$poll->answers[0]->ulid.'"');
    expect($shareContent)->toContain('href="'.url("/p/{$poll->ulid}?answer=").$poll->answers[1]->ulid.'"');
});

it('generates correct share links for kit platform', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();
    $poll = Poll::factory()
        ->for($user->currentTeam)
        ->has(Answer::factory()->count(2)->sequence(['text' => 'Agree'], ['text' => 'Disagree']))
        ->create(['question' => 'Do you agree?']);

    $component = Livewire::actingAs($user)->test('pages::polls.share', ['poll' => $poll])
        ->set('platform', 'kit');

    $shareContent = $component->get('shareContent');

    expect($shareContent)->toContain('Do you agree?');
    expect($shareContent)->toContain('Agree');
    expect($shareContent)->toContain('Disagree');
    expect($shareContent)->toContain('href="'.url("/p/{$poll->ulid}?answer=").$poll->answers[0]->ulid.'&email={{ subscriber.email_address }}"');
    expect($shareContent)->toContain('href="'.url("/p/{$poll->ulid}?answer=").$poll->answers[1]->ulid.'&email={{ subscriber.email_address }}"');
});

it('displays copy to clipboard button', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();
    $poll = Poll::factory()
        ->for($user->currentTeam)
        ->has(Answer::factory()->count(2))
        ->create();

    $response = actingAs($user)->get("/polls/{$poll->id}/share");

    $response->assertSee('Copy to Clipboard');
});

it('redirects guests to login page', function () {
    $poll = Poll::factory()->create();

    $response = $this->get("/polls/{$poll->id}/share");

    $response->assertRedirect('/login');
});

it('shows 403 for polls that do not belong to user team', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();
    $otherUser = User::factory()->withPersonalTeamAndSubscription()->create();
    $poll = Poll::factory()->for($otherUser->currentTeam)->create();

    $response = actingAs($user)->get("/polls/{$poll->id}/share");

    $response->assertForbidden();
});
