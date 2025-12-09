<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::doc'), Title('Creating Polls')] class extends Component
{
    //
};
?>

<div>
    <flux:text class="mt-0! mb-2 font-medium">Documentation</flux:text>

    <h1>Creating Polls</h1>

    <h2>Add a New Poll</h2>

    <p>
        Creating polls is straightforward and flexible. From the dashboard or polls page, click
        <strong>New poll</strong>
        to get started.
    </p>

    <img src="/assets/images/CleanShot 2025-12-08 at 11.02.56@2x.png" />

    <p>On the create poll page, you'll need to provide:</p>

    <ol>
        <li>
            <strong>Poll name</strong>
            - A descriptive title for your survey
        </li>
        <li>
            <strong>Question</strong>
            - The main question you want to ask
        </li>
        <li>
            <strong>Answers</strong>
            - At least two answer options
        </li>
    </ol>

    <img src="/assets/images/CleanShot 2025-12-08 at 11.05.11@2x.png" />

    <h3>Managing Answers</h3>

    <ul>
        <li>
            <strong>Add answers</strong>
            - Click "Add answer" to create more options
        </li>
        <li>
            <strong>Remove answers</strong>
            - Use the delete button (minimum 2 answers required)
        </li>
        <li>
            <strong>Reorder answers</strong>
            - Drag and drop answers to change their order
        </li>
    </ul>

    <p>Each answer can be customized with individual settings like redirect URLs and feedback fields.</p>

    <h2>Poll Settings</h2>

    <p>After creating a poll, you can configure advanced settings to match your specific needs:</p>

    <h3>Response Settings</h3>

    <ul>
        <li>
            <strong>Accept responses</strong>
            - Toggle whether the poll is currently active
        </li>
        <li>
            <strong>Require email address</strong>
            - Make email collection mandatory
        </li>
        <li>
            <strong>Auto-submit responses</strong>
            - Automatically submit when an answer is selected
        </li>
        <li>
            <strong>Collect feedback</strong>
            - Show a feedback field after submission
        </li>
    </ul>

    <h3>Display Settings</h3>

    <ul>
        <li>
            <strong>Layout</strong>
            - Choose between vertical or horizontal answer arrangement
        </li>
    </ul>

    <h3>After Submission</h3>

    <p>Configure what happens after someone responds:</p>

    <ul>
        <li>
            <strong>Show thank you message</strong>
            - Display a custom message with optional button
        </li>
        <li>
            <strong>Redirect to URL</strong>
            - Automatically redirect users to a custom URL
        </li>
    </ul>

    <h3>Branding</h3>

    <ul>
        <li>
            <strong>Hide AntiPoll branding</strong>
            - Remove AntiPoll branding for a white-label experience
        </li>
    </ul>
</div>
