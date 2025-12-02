<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="lg:bg-zinc-100 dark:bg-zinc-900 dark:lg:bg-zinc-950 dark antialiased">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white lg:bg-zinc-100 dark:bg-zinc-900 dark:lg:bg-zinc-950">
        {{ $slot }}

        @fluxScripts
    </body>
</html>
