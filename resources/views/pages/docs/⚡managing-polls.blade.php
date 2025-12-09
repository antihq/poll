<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::doc'), Title('Managing Polls')] class extends Component
{
    //
};
?>

<div>
    <flux:text class="mt-0! mb-2 font-medium">Documentation</flux:text>

    <h1>Managing Polls</h1>

    <h2>View Poll List</h2>

    <p>The polls page shows all your surveys in a clean table format. Each poll displays:</p>

    <ul>
        <li>Poll name with avatar</li>
        <li>Response count badge</li>
        <li>Creation date</li>
    </ul>

    <img src="/assets/images/CleanShot 2025-12-08 at 11.11.07@2x.png" />

    <h2>View Poll Details</h2>

    <p>Click on any poll to see detailed results and analytics:</p>

    <ul>
        <li>
            <strong>Total responses</strong>
            - Overall response count
        </li>
        <li>
            <strong>Answer breakdown</strong>
            - Percentage and count for each answer option
        </li>
        <li>
            <strong>Visual progress bars</strong>
            - Easy-to-read response distribution
        </li>
    </ul>

    <img src="/assets/images/CleanShot 2025-12-08 at 11.12.03@2x.png" />

    <h2>Edit Polls</h2>

    <p>Need to make changes? You can edit:</p>

    <ul>
        <li>Poll name and question</li>
        <li>Answer options and their order</li>
        <li>Individual answer settings</li>
    </ul>

    <p>Access edit options from the poll details page menu.</p>

    <h2>Answer Settings</h2>

    <p>Each answer can have its own custom settings:</p>

    <h3>Redirect URLs</h3>

    <ul>
        <li>Enable custom redirects for specific answer choices</li>
        <li>Perfect for routing users to different pages based on their response</li>
    </ul>

    <h3>Feedback Fields</h3>

    <ul>
        <li>Show feedback prompts for particular answers</li>
        <li>Customize the feedback field label</li>
        <li>Collect detailed qualitative responses</li>
    </ul>
</div>
