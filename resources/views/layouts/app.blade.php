<x-layouts.app.header :title="$title ?? null">
    <flux:main class="lg:bg-white lg:p-10 dark:lg:bg-zinc-900">
        {{ $slot }}
    </flux:main>

    @auth
        <livewire:teams.create />
    @endauth

    <flux:toast />
</x-layouts.app.header>
