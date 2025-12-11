<?php

use App\Http\Controllers\TeamInvitationAcceptController;
use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::welcome');

Route::livewire('docs', 'pages::docs.welcome');
Route::livewire('docs/getting-started', 'pages::docs.getting-started');
Route::livewire('docs/creating-polls', 'pages::docs.creating-polls');
Route::livewire('docs/managing-polls', 'pages::docs.managing-polls');
Route::livewire('docs/sharing-polls', 'pages::docs.sharing-polls');
Route::livewire('docs/viewing-responses', 'pages::docs.viewing-responses');
Route::livewire('docs/advanced-features', 'pages::docs.advanced-features');
Route::livewire('docs/settings-management', 'pages::docs.settings-management');
Route::livewire('docs/best-practices', 'pages::docs.best-practices');
Route::livewire('docs/technical-resources', 'pages::docs.technical-resources');
Route::livewire('docs/help-support', 'pages::docs.help-support');

Route::livewire('changelog/', 'pages::changelog');

Route::livewire('p/{poll:ulid}', 'pages::p.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard/', 'pages::dashboard');

    Route::livewire('polls/', 'pages::polls.index');
    Route::livewire('polls/create', 'pages::polls.create');
    Route::livewire('polls/{poll}', 'pages::polls.show');
    Route::livewire('polls/{poll}/edit', 'pages::polls.edit');
    Route::livewire('polls/{poll}/settings', 'pages::polls.settings');
    Route::livewire('polls/{poll}/share', 'pages::polls.share');
    Route::livewire('polls/{poll}/answers/{answer}', 'pages::polls.answers.show');
    Route::livewire('answers/{answer}/settings', 'pages::answers.settings');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::livewire('settings/profile', 'pages::settings.profile');
    Route::livewire('settings/password', 'pages::settings.password');
    Route::livewire('settings/appearance', 'pages::settings.appearance');

    Route::livewire('teams/create', 'pages::teams.create');
    Route::livewire('teams/{team}/settings/members', 'pages::teams.settings.members');
    Route::livewire('teams/{team}/settings/general', 'pages::teams.settings.general');
    Route::livewire('teams/{team}', 'pages::teams.settings.general');

    Route::get('teams/invitations/{invitation}/accept', TeamInvitationAcceptController::class)
        ->middleware('signed')
        ->name('teams.invitations.accept');
});

require __DIR__.'/auth.php';

require __DIR__.'/billing.php';
