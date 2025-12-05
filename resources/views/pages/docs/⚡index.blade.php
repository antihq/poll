<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::site'), Title('Getting started')] class extends Component
{
    //
};
?>

<div class="mx-auto max-w-2xl">
    <flux:heading size="xl">Getting Started</flux:heading>

    <div class="mt-6 space-y-6">
        <section>
            <flux:heading size="lg" level="2">1. Create Your First Poll</flux:heading>
            <flux:text class="mt-6 text-base">
                Navigate to
                <flux:link href="/polls/create" wire:navigate>/polls/create</flux:link>
                to create your first poll. You'll need to provide:
            </flux:text>
            <ul class="mt-4 ml-4 list-inside list-disc space-y-2">
                <li><flux:text inline class="text-base">Poll name (internal, not shown publicly)</flux:text></li>
                <li><flux:text inline class="text-base">Your poll question</flux:text></li>
                <li><flux:text inline class="text-base">At least two poll answers</flux:text></li>
            </ul>
        </section>

        <section>
            <flux:heading size="lg" level="2">2. Share Your Poll</flux:heading>
            <flux:text class="mt-6 text-base">
                After creating your poll, click the share button to copy and paste it into any email client. You can
                also embed your poll in Kit.com's newsletter platform, where email addresses will be captured
                automatically.
            </flux:text>
        </section>

        <section>
            <flux:heading size="lg" level="2">3. View Responses</flux:heading>
            <flux:text class="mt-6 text-base">
                When people visit your poll, they can submit their answers. All responses are captured and available in
                your dashboard where you can see:
            </flux:text>
            <ul class="mt-4 ml-4 list-inside list-disc space-y-2">
                <li><flux:text inline class="text-base">Total number of responses received</flux:text></li>
                <li><flux:text inline class="text-base">How many responses each answer received</flux:text></li>
                <li><flux:text inline class="text-base">Percentage breakdown for each answer</flux:text></li>
            </ul>
        </section>
    </div>
</div>
