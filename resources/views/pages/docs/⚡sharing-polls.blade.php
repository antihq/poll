<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::doc'), Title('Sharing Polls')] class extends Component
{
    //
};
?>

<div>
    <flux:text class="mb-2 mt-0! font-medium">Documentation</flux:text>

    <h1>Sharing Polls</h1>

    <h2>Share Your Poll</h2>

    <p>
        This is where AntiPoll shines! Most email platforms either don't support polls or charge extra for them.
        AntiPoll lets you embed interactive polls in ANY email service.
    </p>

    <p>From the share page, you can:</p>

    <ol>
        <li>
            <strong>Select your platform</strong>
            - Choose from Universal, Beehiiv, Brevo, EmailOctopus, Ghost, HubSpot, Kit, Loops, MailerLite, or Sendy
        </li>
        <li>
            <strong>Copy HTML</strong>
            - Get platform-specific HTML with proper email merge tags
        </li>
        <li>
            <strong>Preview</strong>
            - See how your poll will look before sharing
        </li>
    </ol>

    <img src="/assets/images/CleanShot 2025-12-08 at 11.12.36@2x.png" />

    <h3>Why This Matters</h3>

    <ul>
        <li>
            <strong>No platform limitations</strong>
            - Works even if your email service doesn't support polls
        </li>
        <li>
            <strong>No expensive upgrades</strong>
            - Skip the premium plan requirements
        </li>
        <li>
            <strong>Universal compatibility</strong>
            - Works with any email service that supports HTML
        </li>
        <li>
            <strong>Subscriber tracking</strong>
            - Automatically links responses to your email list
        </li>
    </ul>

    <h3>Platform Integration</h3>

    <ul>
        <li>
            <strong>Universal</strong>
            - Works with any email platform including Apple Mail, Gmail, Substack, and more
        </li>
        <li>
            <strong>Email platforms</strong>
            - Automatically links responses to subscribers/contacts for better tracking
        </li>
        <li>
            <strong>Ghost</strong>
            - Special integration for email posts and blog content
        </li>
        <li>
            <strong>HubSpot</strong>
            - Works with any HubSpot plan (no expensive upgrades required)
        </li>
    </ul>

    <p>
        The copied HTML includes your poll question and clickable answer links, ready to paste into email campaigns or
        newsletters.
    </p>
</div>
