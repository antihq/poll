<?php

use App\Models\Answer;
use App\Models\Poll;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('displays the poll share page', function () {
    $user = User::factory()->withPersonalTeamAndSubscription()->create();
    $poll = Poll::factory()->for($user->currentTeam)->has(Answer::factory()->count(3))->create();

    actingAs($user)->get("/polls/{$poll->id}/share")->assertSuccessful();
});

it('generates correct share links for platforms', function (string $platform, string $emailPlaceholder) {
    $user = User::factory()->withPersonalTeam()->create();
    $poll = Poll::factory()
        ->for($user->currentTeam)
        ->has(Answer::factory()->count(2)->sequence(['text' => 'Option A'], ['text' => 'Option B']))
        ->create(['question' => 'Which option do you prefer?']);

    $shareContent = Livewire::actingAs($user)
        ->test('pages::polls.share', ['poll' => $poll])
        ->set('platform', $platform)
        ->get('shareContent');

    $baseUrl = url("/p/{$poll->ulid}?answer=");
    $emailParam = $emailPlaceholder ? "&email={$emailPlaceholder}" : '';

    expect($shareContent)
        ->toContain('Which option do you prefer?')
        ->toContain('Option A')
        ->toContain('Option B')
        ->toContain("href=\"{$baseUrl}{$poll->answers[0]->ulid}{$emailParam}\"")
        ->toContain("href=\"{$baseUrl}{$poll->answers[1]->ulid}{$emailParam}\"");
})->with([
    ['universal', ''],
    ['kit', '{{ subscriber.email_address }}'],
    ['ghost', '{email}'],
    ['hubspot', '{{personalization_token(\'contact.email\',\'\')}}'],
    ['beehiiv', '{{email}}'],
    ['brevo', '{{contact.EMAIL}}'],
    ['emailoctopus', '{{EmailAddress}}'],
    ['loops', '{email}'],
    ['mailerlite', '{email}'],
    ['sendy', '[Email]'],
]);
