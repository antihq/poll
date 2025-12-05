<?php

use App\Models\Poll;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Computed;

new class extends Component
{
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
            ->latest()
            ->take(3)
            ->get();
    }
};
?>

<div class="mx-auto max-w-3xl">
    <header class="flex items-center">
        <div class="flex items-center gap-3">
            <flux:avatar
                :name="$this->team->name"
                color="auto"
                initials:single
                :color:seed="'team-' . $this->team->name"
            />
            <flux:heading class="text-xl">{{ $this->team->name }}</flux:heading>
        </div>
        <flux:spacer />
        <flux:dropdown align="end">
            <flux:button icon:trailing="ellipsis-horizontal" size="sm" variant="subtle" />

            <flux:menu>
                <flux:menu.group heading="Settings">
                    <flux:menu.item
                        href="/teams/{{ $this->team->id }}/settings/general"
                        icon="cog-8-tooth"
                        icon:variant="micro"
                        wire:navigate
                    >
                        General
                    </flux:menu.item>
                    <flux:menu.item
                        href="/teams/{{ $this->team->id }}/settings/members"
                        icon="user-group"
                        icon:variant="micro"
                        wire:navigate
                    >
                        Members
                    </flux:menu.item>
                </flux:menu.group>
                @if ($this->team->subscribed())
                    <flux:menu.group heading="Billing">
                        <flux:menu.item href="/billing-portal" icon="credit-card" icon:variant="micro">
                            Manage
                        </flux:menu.item>
                    </flux:menu.group>
                @endif
            </flux:menu>
        </flux:dropdown>
    </header>

    <flux:spacer class="mt-5" />

    @if ($this->polls->count() > 0)
        <header class="flex items-center">
            <flux:heading size="lg">Recent polls</flux:heading>
            <flux:spacer />
            <flux:button
                href="/polls/create"
                variant="primary"
                color="zinc"
                size="sm"
                icon="plus"
                class="-my-1"
                wire:navigate
            >
                New poll
            </flux:button>
        </header>

        <flux:separator class="mt-3" />

        <flux:table>
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
                                <flux:badge color="zinc" size="sm" inset="top bottom">
                                    {{ $poll->poll_responses_count }}
                                    {{ Str::plural('response', $poll->poll_responses_count) }}
                                </flux:badge>
                            </div>
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
