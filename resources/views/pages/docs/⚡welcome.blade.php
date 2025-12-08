<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::doc'), Title('Welcome to Antipoll')] class extends Component
{
    //
};
?>

<div>
    <flux:text class="mb-2 mt-0! font-medium">Documentation</flux:text>

    <h1>Welcome to Antipoll</h1>

    <p>
        Antipoll is the ultimate solution for embedding polls in newsletters and emails. Many email platforms don't
        support interactive polls, or they require expensive plan upgrades to access polling features. Antipoll
        solves this by letting you embed fully functional polls in any email service - no upgrades required.
    </p>

    <p>
        Whether you're using Beehiiv, Ghost, HubSpot, or any other platform, Antipoll makes it easy to collect
        feedback from your audience without platform limitations.
    </p>

    <p>Now, <a href="/docs/getting-started" wire:navigate>let's get started</a>!</p>
</div>
