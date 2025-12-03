<?php

use App\Models\Poll;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    #[Computed]
    public function organization()
    {
        return Auth::user()->currentOrganization;
    }

    #[Computed]
    public function polls()
    {
        return Poll::query()
            ->where('organization_id', $this->organization->id)
            ->withCount('pollResponses')
            ->paginate(10);
    }
};
?>

<div class="mx-auto max-w-3xl">
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
                            {{ $poll->poll_responses_count }} {{ Str::plural('response', $poll->poll_responses_count) }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell align="end" class="text-xs">
                        {{ $poll->created_at->format('M d') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>

