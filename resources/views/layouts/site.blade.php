<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark antialiased dark:bg-zinc-900">
    <head>
        @include('partials.head', ['title' => (isset($title) ? $title . ' - ' : '') . config('app.name')])
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-900">
        <flux:header container>
            <flux:brand href="/" :name="config('app.name')" wire:navigate>
                <x-slot name="logo">
                    <x-logo class="h-6" />
                </x-slot>
            </flux:brand>

            <flux:navbar>
                <flux:navbar.item href="/docs" :accent="false" wire:navigate>Docs</flux:navbar.item>
                <flux:navbar.item href="/changelog" :accent="false" wire:navigate>Changelog</flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <div class="flex items-center gap-x-5 md:gap-x-8">
                @guest
                    <flux:button href="/login" variant="subtle" size="sm" wire:navigate class="max-lg:hidden">
                        Sign in
                    </flux:button>
                    <flux:button href="/register" variant="primary" size="sm" wire:navigate>
                        Get started today
                    </flux:button>
                @else
                    <flux:button href="/dashboard" size="sm" wire:navigate>Dashboard</flux:button>
                @endguest
            </div>
        </flux:header>

        <flux:main class="lg:bg-white lg:p-10 dark:lg:bg-zinc-900">
            {{ $slot }}
        </flux:main>

        <flux:toast />

        <flux:footer container class="border-zinc-200 lg:border-t dark:border-zinc-700">
            <flux:text class="text-sm/6">
                <flux:link href="/" :accent="false" wire:navigate>{{ config('app.name') }}</flux:link>
                is designed, built, and backed by
                <flux:link href="https://x.com/oliverservinX" :accent="false">Oliver Servín</flux:link>
                . Need help? Send an email to
                <flux:link href="mailto:oliver@antihq.com" :accent="false">oliver@antihq.com</flux:link>
                .
            </flux:text>
        </flux:footer>

        @fluxScripts
    </body>
</html>
