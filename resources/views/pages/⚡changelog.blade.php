<?php

use App\Models\UpdateSubscription;
use App\Notifications\UpdateSubscriptionConfirmationNotification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Layout('layouts::changelog'), Title('Changelog')] class extends Component
{
    //
}; ?>

<div class="space-y-20 sm:space-y-32">
    <x-changelog.entry date="2025-12-03">
        <x-changelog.img src="/assets/images/CleanShot 2025-12-03 at 20.15.15@2x.png" />
        <h2><a href="#changelog-2025-12-03">Poll Sharing and Email Capture</a></h2>
        <p>
            You can now share your polls with others and capture email addresses from respondents. Copy polls to any
            email client using the universal platform format, or paste them directly into Kit.com. When using Kit.com,
            the respondent's email is automatically captured in the poll response.
        </p>
        <h3>
            <flux:icon.sparkles variant="solid" />
            New Features
        </h3>
        <ul>
            <li>Share polls with a dedicated sharing page featuring rich text clipboard support</li>
            <li>Universal platform format for copying polls to any email client</li>
            <li>Kit.com platform integration with automatic email capture</li>
            <li>Capture email addresses from poll respondents</li>
            <li>Pre-select answers via URL query parameters for easy sharing</li>
        </ul>
    </x-changelog.entry>
</div>
