<?php

use Livewire\Component;

use App\Models\Team;
use Flux\Flux;

new class extends Component {
    public Team $team;

    public string $name;

    public function mount()
    {
        $this->authorize('update', $this->team);

        $this->name = $this->team->name;
    }

    public function edit()
    {
        $this->authorize('update', $this->team);

        $this->validate([
            'name' => ['required'],
        ]);

        $this->team->update(['name' => $this->name]);

        Flux::toast(
            heading: __('Saved'),
            text: __('Team updated successfully.'),
            variant: 'success'
        );
    }
}; ?>

<x-slot:breadcrumbs>
    @include('partials.team-settings-breadcrumbs', ['team' => $team, 'current' => __('General')])
</x-slot>

<div>
    @include('partials.team-settings-heading')
    <div class="flex items-start max-md:flex-col">
        @include('partials.team-settings-sidebar', ['team' => $team])
        <flux:separator class="md:hidden" />
        <div class="flex-1 self-stretch max-md:pt-6">
            <header>
                <flux:heading>
                    {{ __('General Settings') }}
                </flux:heading>
                <flux:text class="mt-2">
                    {{ __('Update your team name below to keep your workspace up to date.') }}
                </flux:text>
            </header>
            <form wire:submit="edit" class="mt-6 max-w-lg space-y-6">
                <flux:input wire:model="name" :label="__('Name')" />
                <flux:button type="submit" variant="primary">
                    {{ __('Save') }}
                </flux:button>
            </form>
        </div>
    </div>
</div>
