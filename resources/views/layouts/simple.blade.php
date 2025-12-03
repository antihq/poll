<x-layouts.app.simple :title="$title ?? null">
    <flux:main class="lg:bg-white dark:lg:bg-zinc-900 lg:p-10 h-full">
        {{ $slot }}
    </flux:main>

    <flux:toast />
</x-layouts.app.simple>
