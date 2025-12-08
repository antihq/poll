<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::doc'), Title('Best Practices')] class extends Component
{
    //
};
?>

<div>
    <flux:text class="mb-2 mt-0! font-medium">Advanced</flux:text>

    <h1>Best Practices</h1>

    <h2>Poll Design</h2>

    <ul>
        <li>
            <strong>Keep questions clear and concise</strong>
            - Avoid ambiguity
        </li>
        <li>
            <strong>Limit answer options</strong>
            - Too many choices can overwhelm respondents
        </li>
        <li>
            <strong>Use descriptive poll names</strong>
            - Help yourself stay organized
        </li>
    </ul>

    <h2>Sharing Strategy</h2>

    <ul>
        <li>
            <strong>Choose the right platform</strong>
            - Match your email service provider
        </li>
        <li>
            <strong>Test before sending</strong>
            - Preview your poll in different email clients
        </li>
        <li>
            <strong>Consider mobile users</strong>
            - Ensure polls work well on all devices
        </li>
    </ul>

    <h2>Response Collection</h2>

    <ul>
        <li>
            <strong>Enable email collection</strong>
            - Build your audience while gathering feedback
        </li>
        <li>
            <strong>Use feedback fields</strong>
            - Collect qualitative insights alongside quantitative data
        </li>
        <li>
            <strong>Monitor responses</strong>
            - Stay engaged with your audience's feedback
        </li>
    </ul>
</div>
