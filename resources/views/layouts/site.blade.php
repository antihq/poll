<x-layouts.app.site :title="$title ?? null">
    <flux:main class="lg:bg-white dark:lg:bg-zinc-900 lg:p-10">
        {{ $slot }}
    </flux:main>

    <flux:toast />
</x-layouts.app.site>
