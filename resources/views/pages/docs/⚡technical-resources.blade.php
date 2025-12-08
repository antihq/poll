<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::doc'), Title('Technical Resources')] class extends Component
{
    //
};
?>

<div>
    <flux:text class="mt-0! mb-2 font-medium">Advanced</flux:text>

    <h1>Technical Resources</h1>

    <h2>Platform Integration</h2>

    <p>AntiPoll integrates seamlessly with major email platforms:</p>

    <ul>
        <li>
            <strong>Beehiiv</strong>
            -
            <code>@{{ email }}</code>
            merge tag
        </li>
        <li>
            <strong>Brevo</strong>
            -
            <code>@{{ contact.EMAIL }}</code>
            merge tag
        </li>
        <li>
            <strong>EmailOctopus</strong>
            -
            <code>@{{ EmailAddress }}</code>
            merge tag
        </li>
        <li>
            <strong>Ghost</strong>
            -
            <code>{email}</code>
            merge tag
        </li>
        <li>
            <strong>HubSpot</strong>
            -
            <code>@{{ personalization_token('contact.email','') }}</code>
            merge tag
        </li>
        <li>
            <strong>Kit</strong>
            -
            <code>@{{ subscriber.email_address }}</code>
            merge tag
        </li>
        <li>
            <strong>Loops</strong>
            -
            <code>{email}</code>
            merge tag
        </li>
        <li>
            <strong>MailerLite</strong>
            -
            <code>{email}</code>
            merge tag
        </li>
        <li>
            <strong>Sendy</strong>
            -
            <code>[Email]</code>
            merge tag
        </li>
    </ul>

    <h2>URL Parameters</h2>

    <p>Enhance your polls with URL parameters:</p>

    <ul>
        <li>
            <code>?answer={ulid}</code>
            - Preselect an answer
        </li>
        <li>
            <code>?email={email}</code>
            - Autofill email address
        </li>
    </ul>
</div>
