<flux:breadcrumbs>
    <flux:breadcrumbs.item href="{{ route('dashboard') }}" wire:navigate>{{ $team->name }}</flux:breadcrumbs.item>
    <flux:breadcrumbs.item href="{{ route('teams.settings.general', $team) }}" wire:navigate>
        Settings
    </flux:breadcrumbs.item>
    <flux:breadcrumbs.item>{{ $current }}</flux:breadcrumbs.item>
</flux:breadcrumbs>
