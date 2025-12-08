<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::doc'), Title('Advanced Features')] class extends Component
{
    //
};
?>

<div>
    <flux:text class="mb-2 mt-0! font-medium">Advanced</flux:text>

    <h1>Advanced Features</h1>

    <h2>Auto-Submit</h2>

    <p>Enable auto-submit to create seamless one-click polls:</p>

    <ul>
        <li>Perfect for quick feedback collection</li>
        <li>Reduces friction for respondents</li>
        <li>Ideal for email campaigns where engagement is key</li>
    </ul>

    <h2>Email Collection</h2>

    <p>Collect email addresses to:</p>

    <ul>
        <li>Build your subscriber list</li>
        <li>Follow up with respondents</li>
        <li>Segment your audience based on responses</li>
    </ul>

    <h2>Custom Branding</h2>

    <p>Remove Antipoll branding to:</p>

    <ul>
        <li>Maintain brand consistency</li>
        <li>Create white-label experiences</li>
        <li>Use polls in professional contexts</li>
    </ul>
</div>
