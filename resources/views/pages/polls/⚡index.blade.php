<?php

use App\Models\Poll;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('All polls')] class extends Component
{
    use WithPagination;

    #[Computed]
    public function team()
    {
        return Auth::user()->currentTeam;
    }

    #[Computed]
    public function polls()
    {
        return Poll::query()
            ->where('team_id', $this->team->id)
            ->withCount('pollResponses')
            ->paginate(10);
    }
};
?>

<div class="mx-auto max-w-3xl">
    @if ($this->polls->count() > 0)
        <header class="flex items-center">
            <flux:heading class="text-xl">All polls</flux:heading>
            <flux:spacer />
            <flux:button href="/polls/create" variant="primary" color="zinc" size="sm" icon="plus" wire:navigate>
                New poll
            </flux:button>
        </header>

        <flux:separator class="mt-6" />

        <flux:table :paginate="$this->polls" wire:poll>
            <flux:table.rows>
                @foreach ($this->polls as $poll)
                    <flux:table.row :key="$poll->id">
                        <flux:table.cell class="w-full">
                            <div class="flex items-center gap-3">
                                <flux:avatar
                                    :name="strtoupper($poll->name)"
                                    size="xs"
                                    color="auto"
                                    initials:single
                                    :color:seed="'poll-'.$poll->id"
                                />
                                <flux:link :href="'/polls/'.$poll->id" :accent="false" wire:navigate>
                                    {{ $poll->name }}
                                </flux:link>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            <flux:badge color="zinc" size="sm" inset="top bottom">
                                {{ $poll->poll_responses_count }}
                                {{ Str::plural('response', $poll->poll_responses_count) }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell align="end" class="text-xs">
                            {{ $poll->created_at->format('M d') }}
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @else
        <flux:callout icon="chart-bar" inline>
            <flux:callout.heading>No polls yet</flux:callout.heading>
            <flux:callout.text>
                Create your first poll to start collecting responses from your audience.
            </flux:callout.text>
            <x-slot name="actions">
                <flux:button href="/polls/create" variant="primary" color="zinc" size="sm" icon="plus" wire:navigate>
                    New poll
                </flux:button>
            </x-slot>
        </flux:callout>
    @endif
</div>
