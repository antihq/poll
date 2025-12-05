<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="lg:bg-zinc-100 dark:bg-zinc-900 dark:lg:bg-zinc-950 dark antialiased">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-900">
        <flux:main class="lg:bg-white dark:lg:bg-zinc-900 lg:p-10 h-full">
            {{ $slot }}
        </flux:main>

        @fluxScripts
    </body>
</html>
