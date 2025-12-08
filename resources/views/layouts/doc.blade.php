<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark antialiased dark:bg-zinc-900">
    <head>
        @include('partials.head', ['title' => (isset($title) ? $title.' - ' : '').config('app.name')])
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-900">
        <flux:header class="border-b border-zinc-100 dark:border-zinc-800">
            <flux:brand href="/" logo="/logo.png" :name="config('app.name')" wire:navigate />

            <flux:spacer />

            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="right" />

            <div class="flex items-center gap-x-5 md:gap-x-8 max-lg:hidden">
                @guest
                    <flux:button href="/login" size="sm" variant="primary" color="zinc" icon:trailing="arrow-right-circle" wire:navigate class="max-lg:hidden">
                        Sign in
                    </flux:button>
                @else
                    <flux:button href="/dashboard" size="sm" variant="primary" color="zinc" icon:trailing="arrow-right-circle" wire:navigate>Dashboard</flux:button>
                @endguest
            </div>
        </flux:header>

        <flux:sidebar collapsible="mobile" class="bg-zinc-25 border-r border-zinc-100 dark:border-zinc-800 dark:bg-zinc-900">
            <flux:sidebar.header class="lg:hidden">
                <flux:sidebar.brand
                    href="/"
                    logo="/logo.png"
                    :name="config('app.name')"
                    wire:navigate
                />
                <flux:sidebar.collapse />
            </flux:sidebar.header>

            <flux:sidebar.nav class="lg:hidden">
                @guest
                    <flux:sidebar.item icon="arrow-right-end-on-rectangle" href="/login" :accent="false" wire:navigate>Sign in</flux:sidebar.item>
                @else
                    <flux:sidebar.item icon="home" href="/dashboard" :accent="false" wire:navigate>Dashboard</flux:sidebar.item>
                @endguest
            </flux:sidebar.nav>

            <flux:sidebar.nav>
                <flux:sidebar.group heading="Documentation" expandable>
                    <flux:sidebar.item href="/docs" :accent="false" :current="request()->is('docs')">
                        Welcome
                    </flux:sidebar.item>
                    <flux:sidebar.item href="/docs/getting-started" :accent="false" wire:navigate>
                        Getting Started
                    </flux:sidebar.item>
                    <flux:sidebar.item href="/docs/creating-polls" :accent="false" wire:navigate>
                        Creating Polls
                    </flux:sidebar.item>
                    <flux:sidebar.item href="/docs/managing-polls" :accent="false" wire:navigate>
                        Managing Polls
                    </flux:sidebar.item>
                    <flux:sidebar.item href="/docs/sharing-polls" :accent="false" wire:navigate>
                        Sharing Polls
                    </flux:sidebar.item>
                    <flux:sidebar.item href="/docs/viewing-responses" :accent="false" wire:navigate>
                        Viewing Responses
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:navlist.group heading="Advanced" class="mt-6" expandable>
                    <flux:sidebar.item href="/docs/advanced-features" :accent="false" wire:navigate>
                        Advanced Features
                    </flux:sidebar.item>
                    <flux:sidebar.item href="/docs/settings-management" :accent="false" wire:navigate>
                        Settings & Management
                    </flux:sidebar.item>
                    <flux:sidebar.item href="/docs/best-practices" :accent="false" wire:navigate>
                        Best Practices
                    </flux:sidebar.item>
                    <flux:sidebar.item href="/docs/technical-resources" :accent="false" wire:navigate>
                        Technical Resources
                    </flux:sidebar.item>
                </flux:navlist.group>

                <flux:navlist.group heading="Support" class="mt-6" expandable>
                    <flux:sidebar.item href="/docs/help-support" :accent="false" wire:navigate>
                        Help & Support
                    </flux:sidebar.item>
                </flux:navlist.group>
            </flux:sidebar.nav>
        </flux:sidebar>

        <flux:main class="lg:bg-white dark:lg:bg-zinc-900">
            <div class="prose prose-sm prose-zinc prose-pre:!bg-zinc-100 prose-pre:text-base prose-th:text-left mx-auto w-full max-w-2xl first:mt-0">{{ $slot }}</div>
        </flux:main>

        <flux:footer class="border-zinc-100 lg:border-t dark:border-zinc-800">
            <flux:text class="text-xs/6 lg:text-sm/6">
                <flux:link href="/" :accent="false" wire:navigate>{{ config('app.name') }}</flux:link>
                is designed, built, and backed by
                <flux:link href="https://x.com/oliverservinX" :accent="false">Oliver Servín</flux:link>
                . Need help? Send an email to
                <flux:link href="mailto:oliver@antihq.com" :accent="false">oliver@antihq.com</flux:link>
                .
            </flux:text>
        </flux:footer>

        <flux:toast />

        @fluxScripts
    </body>
</html>
