<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::doc'), Title('Welcome to AntiPoll')] class extends Component
{
    //
};
?>

<div>
    <flux:text class="mt-0! mb-2 font-medium">Documentation</flux:text>

    <h1>Welcome to AntiPoll</h1>

    <p>
        AntiPoll is the ultimate solution for embedding polls in newsletters and emails. Many email platforms don't
        support interactive polls, or they require expensive plan upgrades to access polling features. AntiPoll solves
        this by letting you embed fully functional polls in any email service - no upgrades required.
    </p>

    <p>
        Whether you're using Beehiiv, Ghost, HubSpot, or any other platform, AntiPoll makes it easy to collect feedback
        from your audience without platform limitations.
    </p>

    <p>
        Now,
        <a href="/docs/getting-started" wire:navigate>let's get started</a>
        !
    </p>
</div>
