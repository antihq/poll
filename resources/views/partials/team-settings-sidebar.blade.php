<div class="me-10 w-full pb-4 md:w-[220px]">
    <flux:navlist>
        <flux:navlist.item
            :href="route('teams.settings.general', $team)"
            :current="request()->routeIs('teams.settings.general')"
            wire:navigate
        >
            General
        </flux:navlist.item>
        <flux:navlist.item
            :href="route('teams.settings.members', $team)"
            :current="request()->routeIs('teams.settings.members')"
            wire:navigate
        >
            Members
        </flux:navlist.item>
    </flux:navlist>
</div>
