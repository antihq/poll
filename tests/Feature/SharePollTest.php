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

it('generates correct share links for ghost platform', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();
    $poll = Poll::factory()
        ->for($user->currentTeam)
        ->has(Answer::factory()->count(2)->sequence(['text' => 'Love it'], ['text' => 'Hate it']))
        ->create(['question' => 'What do you think?']);

    $component = Livewire::actingAs($user)->test('pages::polls.share', ['poll' => $poll])
        ->set('platform', 'ghost');

    $shareContent = $component->get('shareContent');

    expect($shareContent)->toContain('What do you think?');
    expect($shareContent)->toContain('Love it');
    expect($shareContent)->toContain('Hate it');
    expect($shareContent)->toContain('href="'.url("/p/{$poll->ulid}?answer=").$poll->answers[0]->ulid.'&email={email}"');
    expect($shareContent)->toContain('href="'.url("/p/{$poll->ulid}?answer=").$poll->answers[1]->ulid.'&email={email}"');
});

it('generates correct share links for hubspot platform', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();
    $poll = Poll::factory()
        ->for($user->currentTeam)
        ->has(Answer::factory()->count(2)->sequence(['text' => 'Yes'], ['text' => 'No']))
        ->create(['question' => 'Would you recommend us?']);

    $component = Livewire::actingAs($user)->test('pages::polls.share', ['poll' => $poll])
        ->set('platform', 'hubspot');

    $shareContent = $component->get('shareContent');

    expect($shareContent)->toContain('Would you recommend us?');
    expect($shareContent)->toContain('Yes');
    expect($shareContent)->toContain('No');
    expect($shareContent)->toContain('href="'.url("/p/{$poll->ulid}?answer=").$poll->answers[0]->ulid.'&email={{personalization_token(\'contact.email\',\'\')}}"');
    expect($shareContent)->toContain('href="'.url("/p/{$poll->ulid}?answer=").$poll->answers[1]->ulid.'&email={{personalization_token(\'contact.email\',\'\')}}"');
});

it('generates correct share links for beehiiv platform', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();
    $poll = Poll::factory()
        ->for($user->currentTeam)
        ->has(Answer::factory()->count(2)->sequence(['text' => 'Option 1'], ['text' => 'Option 2']))
        ->create(['question' => 'Which do you prefer?']);

    $component = Livewire::actingAs($user)->test('pages::polls.share', ['poll' => $poll])
        ->set('platform', 'beehiiv');

    $shareContent = $component->get('shareContent');

    expect($shareContent)->toContain('Which do you prefer?');
    expect($shareContent)->toContain('Option 1');
    expect($shareContent)->toContain('Option 2');
    expect($shareContent)->toContain('href="'.url("/p/{$poll->ulid}?answer=").$poll->answers[0]->ulid.'&email={{email}}"');
    expect($shareContent)->toContain('href="'.url("/p/{$poll->ulid}?answer=").$poll->answers[1]->ulid.'&email={{email}}"');
});

it('generates correct share links for brevo platform', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();
    $poll = Poll::factory()
        ->for($user->currentTeam)
        ->has(Answer::factory()->count(2)->sequence(['text' => 'Yes'], ['text' => 'No']))
        ->create(['question' => 'Do you agree?']);

    $component = Livewire::actingAs($user)->test('pages::polls.share', ['poll' => $poll])
        ->set('platform', 'brevo');

    $shareContent = $component->get('shareContent');

    expect($shareContent)->toContain('Do you agree?');
    expect($shareContent)->toContain('Yes');
    expect($shareContent)->toContain('No');
    expect($shareContent)->toContain('href="'.url("/p/{$poll->ulid}?answer=").$poll->answers[0]->ulid.'&email={{contact.EMAIL}}"');
    expect($shareContent)->toContain('href="'.url("/p/{$poll->ulid}?answer=").$poll->answers[1]->ulid.'&email={{contact.EMAIL}}"');
});

it('generates correct share links for emailoctopus platform', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();
    $poll = Poll::factory()
        ->for($user->currentTeam)
        ->has(Answer::factory()->count(2)->sequence(['text' => 'Love it'], ['text' => 'Hate it']))
        ->create(['question' => 'What do you think?']);

    $component = Livewire::actingAs($user)->test('pages::polls.share', ['poll' => $poll])
        ->set('platform', 'emailoctopus');

    $shareContent = $component->get('shareContent');

    expect($shareContent)->toContain('What do you think?');
    expect($shareContent)->toContain('Love it');
    expect($shareContent)->toContain('Hate it');
    expect($shareContent)->toContain('href="'.url("/p/{$poll->ulid}?answer=").$poll->answers[0]->ulid.'&email={{EmailAddress}}"');
    expect($shareContent)->toContain('href="'.url("/p/{$poll->ulid}?answer=").$poll->answers[1]->ulid.'&email={{EmailAddress}}"');
});

it('generates correct share links for loops platform', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();
    $poll = Poll::factory()
        ->for($user->currentTeam)
        ->has(Answer::factory()->count(2)->sequence(['text' => 'Option A'], ['text' => 'Option B']))
        ->create(['question' => 'Which option?']);

    $component = Livewire::actingAs($user)->test('pages::polls.share', ['poll' => $poll])
        ->set('platform', 'loops');

    $shareContent = $component->get('shareContent');

    expect($shareContent)->toContain('Which option?');
    expect($shareContent)->toContain('Option A');
    expect($shareContent)->toContain('Option B');
    expect($shareContent)->toContain('href="'.url("/p/{$poll->ulid}?answer=").$poll->answers[0]->ulid.'&email={email}"');
    expect($shareContent)->toContain('href="'.url("/p/{$poll->ulid}?answer=").$poll->answers[1]->ulid.'&email={email}"');
});

it('generates correct share links for mailerlite platform', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();
    $poll = Poll::factory()
        ->for($user->currentTeam)
        ->has(Answer::factory()->count(2)->sequence(['text' => 'Good'], ['text' => 'Bad']))
        ->create(['question' => 'How is it?']);

    $component = Livewire::actingAs($user)->test('pages::polls.share', ['poll' => $poll])
        ->set('platform', 'mailerlite');

    $shareContent = $component->get('shareContent');

    expect($shareContent)->toContain('How is it?');
    expect($shareContent)->toContain('Good');
    expect($shareContent)->toContain('Bad');
    expect($shareContent)->toContain('href="'.url("/p/{$poll->ulid}?answer=").$poll->answers[0]->ulid.'&email={email}"');
    expect($shareContent)->toContain('href="'.url("/p/{$poll->ulid}?answer=").$poll->answers[1]->ulid.'&email={email}"');
});

it('generates correct share links for sendy platform', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalTeamAndSubscription()->create();
    $poll = Poll::factory()
        ->for($user->currentTeam)
        ->has(Answer::factory()->count(2)->sequence(['text' => 'Subscribe'], ['text' => 'Unsubscribe']))
        ->create(['question' => 'What would you like to do?']);

    $component = Livewire::actingAs($user)->test('pages::polls.share', ['poll' => $poll])
        ->set('platform', 'sendy');

    $shareContent = $component->get('shareContent');

    expect($shareContent)->toContain('What would you like to do?');
    expect($shareContent)->toContain('Subscribe');
    expect($shareContent)->toContain('Unsubscribe');
    expect($shareContent)->toContain('href="'.url("/p/{$poll->ulid}?answer=").$poll->answers[0]->ulid.'&email=[Email]"');
    expect($shareContent)->toContain('href="'.url("/p/{$poll->ulid}?answer=").$poll->answers[1]->ulid.'&email=[Email]"');
});
