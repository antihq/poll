<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::doc'), Title('Settings & Management')] class extends Component
{
    //
};
?>

<div>
    <flux:text class="mb-2 mt-0! font-medium">Advanced</flux:text>

    <h1>Settings & Management</h1>

    <h2>Profile Settings</h2>

    <p>Manage your account information:</p>

    <ul>
        <li>Update your name and email</li>
        <li>Upload profile avatar</li>
        <li>Configure appearance preferences</li>
    </ul>

    <h2>Team Management</h2>

    <p>For team accounts:</p>

    <ul>
        <li>Invite team members</li>
        <li>Manage permissions</li>
        <li>Switch between teams</li>
    </ul>

    <h2>Poll Management</h2>

    <p>Full control over your polls:</p>

    <ul>
        <li>Edit poll details at any time</li>
        <li>Delete polls when no longer needed</li>
        <li>Configure individual answer settings</li>
    </ul>
</div>
