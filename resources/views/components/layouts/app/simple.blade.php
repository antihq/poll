<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="lg:bg-zinc-100 dark:bg-zinc-900 dark:lg:bg-zinc-950 dark antialiased">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-900">
        <flux:header>
            <flux:spacer />
            <flux:brand href="/" :name="config('app.name')" wire:navigate>
                <x-slot name="logo">
                    <x-logo class="h-6" />
                </x-slot>
            </flux:brand>
            <flux:spacer />
        </flux:header>

        {{ $slot }}

        <flux:footer>
            <flux:text class="text-sm/6 text-center">
                <flux:link href="/" :accent="false" wire:navigate>{{ config('app.name') }}</flux:link>
                is designed, built, and backed by
                <flux:link href="https://x.com/oliverservinX" :accent="false">Oliver Servín</flux:link>
                . Problems or questions? Contact
                <flux:link href="mailto:support@antihq.com" :accent="false">support@antihq.com</flux:link>
                .
            </flux:text>
        </flux:footer>

        @fluxScripts
    </body>
</html>
