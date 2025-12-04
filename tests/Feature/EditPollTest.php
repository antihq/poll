<?php

use App\Models\Answer;
use App\Models\Poll;
use App\Models\Team;
use App\Models\User;
use Livewire\Livewire;

test('it displays the poll edit page', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->current_team_id = $team->id;
    $user->save();

    $poll = Poll::factory()->create(['team_id' => $team->id, 'name' => 'Test Poll', 'question' => 'Test Question?']);
    Answer::factory()->create(['poll_id' => $poll->id, 'text' => 'Answer 1', 'sort_order' => 0]);
    Answer::factory()->create(['poll_id' => $poll->id, 'text' => 'Answer 2', 'sort_order' => 1]);

    $component = Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => $poll]);

    $component->assertStatus(200);
    $component->assertSee('Edit poll');
    $component->assertSet('name', 'Test Poll');
    $component->assertSet('question', 'Test Question?');
    $component->assertSet('answers', ['Answer 1', 'Answer 2']);
});

test('it updates a poll with valid data', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->current_team_id = $team->id;
    $user->save();

    $poll = Poll::factory()->create(['team_id' => $team->id]);
    $answer1 = Answer::factory()->create(['poll_id' => $poll->id, 'text' => 'Answer 1', 'sort_order' => 0]);
    $answer2 = Answer::factory()->create(['poll_id' => $poll->id, 'text' => 'Answer 2', 'sort_order' => 1]);

    Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => $poll])
        ->set('name', 'Updated Poll Name')
        ->set('question', 'Updated question?')
        ->set('answers', ['Updated Answer 1', 'Updated Answer 2'])
        ->call('update');

    $this->assertDatabaseHas('polls', [
        'id' => $poll->id,
        'name' => 'Updated Poll Name',
        'question' => 'Updated question?',
    ]);

    $this->assertDatabaseHas('answers', [
        'id' => $answer1->id,
        'text' => 'Updated Answer 1',
        'sort_order' => 0,
    ]);

    $this->assertDatabaseHas('answers', [
        'id' => $answer2->id,
        'text' => 'Updated Answer 2',
        'sort_order' => 1,
    ]);
});

test('it can add new answers when editing', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->current_team_id = $team->id;
    $user->save();

    $poll = Poll::factory()->create(['team_id' => $team->id]);
    Answer::factory()->create(['poll_id' => $poll->id, 'text' => 'Answer 1', 'sort_order' => 0]);
    Answer::factory()->create(['poll_id' => $poll->id, 'text' => 'Answer 2', 'sort_order' => 1]);

    Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => $poll])
        ->set('name', $poll->name)
        ->set('question', $poll->question)
        ->set('answers', ['Answer 1', 'Answer 2', 'New Answer 3'])
        ->call('update');

    $this->assertDatabaseHas('answers', [
        'poll_id' => $poll->id,
        'text' => 'New Answer 3',
        'sort_order' => 2,
    ]);
});

test('it can remove answers when editing', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->current_team_id = $team->id;
    $user->save();

    $poll = Poll::factory()->create(['team_id' => $team->id]);
    $answer1 = Answer::factory()->create(['poll_id' => $poll->id, 'text' => 'Answer 1', 'sort_order' => 0]);
    $answer2 = Answer::factory()->create(['poll_id' => $poll->id, 'text' => 'Answer 2', 'sort_order' => 1]);
    $answer3 = Answer::factory()->create(['poll_id' => $poll->id, 'text' => 'Answer 3', 'sort_order' => 2]);

    Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => $poll])
        ->set('name', $poll->name)
        ->set('question', $poll->question)
        ->set('answers', ['Answer 1', 'Answer 2'])
        ->call('update');

    $this->assertDatabaseMissing('answers', [
        'id' => $answer3->id,
    ]);
});

test('it shows validation errors when less than two answers are provided', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->current_team_id = $team->id;
    $user->save();

    $poll = Poll::factory()->create(['team_id' => $team->id]);
    Answer::factory()->create(['poll_id' => $poll->id, 'text' => 'Answer 1', 'sort_order' => 0]);
    Answer::factory()->create(['poll_id' => $poll->id, 'text' => 'Answer 2', 'sort_order' => 1]);

    Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => $poll])
        ->set('name', $poll->name)
        ->set('question', $poll->question)
        ->set('answers', ['Answer 1'])
        ->call('update')
        ->assertHasErrors(['answers']);
});

test('it redirects guests to login page', function () {
    $poll = Poll::factory()->create();

    $response = $this->get("/polls/{$poll->id}/edit");

    $response->assertRedirect('/login');
});

test('it shows 403 for polls that do not belong to user team', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->current_team_id = $team->id;
    $user->save();

    $otherTeam = Team::factory()->create();
    $poll = Poll::factory()->create(['team_id' => $otherTeam->id]);

    Livewire::actingAs($user)
        ->test('pages::polls.edit', ['poll' => $poll])
        ->assertStatus(403);
});
