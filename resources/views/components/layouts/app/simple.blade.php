<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="lg:bg-zinc-100 dark:bg-zinc-900 dark:lg:bg-zinc-950 dark antialiased">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-900">
        <flux:header>
            <flux:spacer />
            <flux:brand href="/" :name="config('app.name')">
                <x-slot name="logo">
                    <x-logo class="h-6" />
                </x-slot>
            </flux:brand>
            <flux:spacer />
        </flux:header>

        {{ $slot }}

        <flux:footer>
            <div class="text-center">
                <flux:text class="text-sm/6">
                    Built with
                    <flux:icon.heart variant="micro" class="inline" />
                    by
                    <flux:link href="https://x.com/oliverservinX" :accent="false">Oliver Servín</flux:link>
                </flux:text>
                <flux:text class="mt-6 lg:mt-8 text-sm/6">
                    &copy; {{ date('Y') }} Anti Software. All rights reserved. Problems or questions? Contact <
                    <flux:link href="mailto:support@antihq.com" :accent="false">support@antihq.com</flux:link>
                    >.
                </flux:text>
            </div>
        </flux:footer>

        @fluxScripts
    </body>
</html>
