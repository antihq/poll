<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="dark antialiased lg:bg-zinc-100 dark:bg-zinc-900 dark:lg:bg-zinc-950"
>
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white lg:bg-zinc-100 dark:bg-zinc-900 dark:lg:bg-zinc-950">
        <flux:header class="border-zinc-200 lg:border-b dark:border-zinc-700">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" size="sm" />

            @auth
                <div class="flex h-full items-center max-lg:hidden">
                    <livewire:teams-dropdown />
                    <flux:separator vertical class="mx-1 my-5" />
                </div>
            @endauth

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item href="/polls" :current="request()->routeIs('polls.*')" :accent="false" wire:navigate>
                    Polls
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            @auth
                <flux:dropdown position="top" align="end">
                    <flux:button size="sm" variant="ghost" square>
                        <flux:avatar size="xs" :name="Auth::user()->name" color="auto" initials:single />
                    </flux:button>

                    <flux:menu>
                        <flux:menu.item href="/settings/profile" icon="cog-8-tooth" icon:variant="micro" wire:navigate>
                            Settings
                        </flux:menu.item>

                        <flux:menu.separator />

                        <form method="POST" action="/logout" class="w-full">
                            @csrf
                            <flux:menu.item
                                as="button"
                                type="submit"
                                icon="arrow-right-start-on-rectangle"
                                icon:variant="micro"
                                class="w-full"
                            >
                                Log Out
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            @else
                <flux:button href="/dashboard" variant="subtle">Account</flux:button>
            @endauth
        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar
            stashable
            sticky
            class="border-e border-zinc-200 bg-white lg:hidden dark:border-zinc-700 dark:bg-zinc-900"
        >
            <flux:sidebar.header>
                <livewire:teams-dropdown />

                <flux:sidebar.collapse
                    class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2"
                />
            </flux:sidebar.header>

            <flux:separator variant="subtle" />

            <flux:sidebar.nav>
                @auth
                    <flux:sidebar.item href="/polls" :accent="false" wire:navigate>Polls</flux:sidebar.item>
                @endauth
            </flux:sidebar.nav>
        </flux:sidebar>

        {{ $slot }}

        <flux:footer class="border-zinc-200 lg:border-t dark:border-zinc-700">
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
