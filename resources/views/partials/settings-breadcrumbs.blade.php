<flux:breadcrumbs>
    <flux:breadcrumbs.item href="{{ route('settings.profile') }}" wire:navigate>Settings</flux:breadcrumbs.item>
    <flux:breadcrumbs.item>{{ $current ?? '' }}</flux:breadcrumbs.item>
</flux:breadcrumbs>
