<?php

use App\Http\Middleware\EnsureUserIsSubscribed;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::livewire('docs/', 'pages::docs.index');

Route::livewire('changelog/', 'pages::changelog');

Route::livewire('p/{poll:ulid}', 'pages::p.show');

Route::middleware(['auth', 'verified', EnsureUserIsSubscribed::class])->group(function () {
    Route::redirect('dashboard', 'polls');

    Route::livewire('polls/', 'pages::polls.index');
    Route::livewire('polls/create', 'pages::polls.create');
    Route::livewire('polls/{poll}', 'pages::polls.show');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::livewire('settings/profile', 'pages::settings.profile')->name('settings.profile');
    Route::livewire('settings/password', 'pages::settings.password')->name('settings.password');
    Route::livewire('settings/appearance', 'pages::settings.appearance')->name('settings.appearance');

    Route::livewire('teams/{team}/settings/members', 'pages::teams.settings.members')
        ->name('teams.settings.members');
    Route::livewire('teams/{team}/settings/general', 'pages::teams.settings.general')
        ->name('teams.settings.general');
    Route::livewire('teams/{team}', 'pages::teams.settings.general')
        ->name('teams.show');

    Route::get('teams/invitations/{invitation}/accept', \App\Http\Controllers\TeamInvitationAcceptController::class)
        ->middleware('signed')
        ->name('teams.invitations.accept');
});

require __DIR__.'/auth.php';

require __DIR__.'/billing.php';
