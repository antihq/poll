<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="dark antialiased lg:bg-zinc-100 dark:bg-zinc-900 dark:lg:bg-zinc-950"
>
    <head>
        @include('partials.head', ['title' => (isset($title) ? $title.' - ' : '').config('app.name')])
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-900">
        <flux:main class="h-full lg:bg-white lg:p-10 dark:lg:bg-zinc-900">
            {{ $slot }}
        </flux:main>

        @fluxScripts
    </body>
</html>
