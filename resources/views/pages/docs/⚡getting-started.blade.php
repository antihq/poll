<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::doc'), Title('Getting Started')] class extends Component
{
    //
};
?>

<div>
    <flux:text class="mb-2 mt-0! font-medium">Documentation</flux:text>

    <h1>Getting Started</h1>

    <h2>Dashboard</h2>

    <p>
        After logging in, you'll land on the Dashboard where you can see all your polls at a glance. The dashboard shows
        your poll list with response counts and creation dates, making it easy to track engagement across all your
        surveys.
    </p>

    <img src="/assets/images/CleanShot 2025-12-08 at 10.58.18@2x.png" />

    <p>
        From the dashboard, you can quickly create new polls, view existing ones, and monitor response activity. Each
        poll displays its name, response count, and creation date for easy reference.
    </p>

    <h2>Navigation</h2>

    <p>Getting around Antipoll is simple and intuitive. Use the main navigation to access different sections:</p>

    <ul>
        <li>
            <strong>Dashboard</strong>
            - View all your polls
        </li>
        <li>
            <strong>Polls</strong>
            - Create and manage your surveys
        </li>
        <li>
            <strong>Settings</strong>
            - Manage your profile and team settings
        </li>
    </ul>

    <p>
        The interface is clean and focused, helping you get to what you need without unnecessary clicks or complexity.
    </p>
</div>
