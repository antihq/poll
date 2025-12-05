<?php

use App\Models\Poll;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public Poll $poll;

    public function mount(): void
    {
        $this->authorize('view', $this->poll);

        $this->poll->load(['answers' => function ($query) {
            $query->orderBy('sort_order');
        }]);
    }

    #[Computed]
    public function totalResponses(): int
    {
        return $this->poll->pollResponses()->count();
    }

    #[Computed]
    public function responseCounts(): array
    {
        $totalResponses = $this->totalResponses;

        return $this->poll->answers->mapWithKeys(function ($answer) use ($totalResponses) {
            $responseCount = $answer->pollResponses()->count();
            $percentage = $totalResponses > 0 ? round(($responseCount / $totalResponses) * 100) : 0;

            return [
                $answer->id => [
                    'count' => $responseCount,
                    'percentage' => $percentage,
                ],
            ];
        })->all();
    }
}; ?>

<div class="mx-auto max-w-3xl">
    <header class="flex items-center">
        <div class="flex items-center gap-3">
            <flux:avatar :name="strtoupper($poll->name)" color="auto" initials:single :color:seed="'poll-'.$poll->id" />
            <flux:heading class="text-xl">{{ $poll->name }}</flux:heading>
        </div>
        <flux:spacer />
        <flux:dropdown align="end">
            <flux:button icon:trailing="ellipsis-horizontal" size="sm" variant="subtle" />

            <flux:menu>
                <flux:menu.item href="/polls/{{ $poll->id }}/edit" icon="pencil" icon:variant="micro" wire:navigate>
                    Edit Poll
                </flux:menu.item>
                <flux:menu.item
                    href="/polls/{{ $poll->id }}/settings"
                    icon="cog-6-tooth"
                    icon:variant="micro"
                    wire:navigate
                >
                    Settings
                </flux:menu.item>
                <flux:menu.item href="/polls/{{ $poll->id }}/share" icon="share" icon:variant="micro" wire:navigate>
                    Share Poll
                </flux:menu.item>
                <flux:menu.item
                    href="/p/{{ $poll->ulid }}"
                    icon="arrow-top-right-on-square"
                    icon:variant="micro"
                    target="_blank"
                >
                    View Public Link
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </header>

    <flux:text class="mt-4">
        {{ $poll->question }}
    </flux:text>

    <flux:spacer class="mt-8" />

    @if ($this->totalResponses > 0)
        <header class="flex items-center">
            <flux:heading size="lg">
                {{ $this->totalResponses }} {{ Str::plural('response', $this->totalResponses) }}
            </flux:heading>
        </header>

        <flux:separator class="mt-3" />

        <flux:table>
            <flux:table.rows>
                @foreach ($poll->answers as $answer)
                    <flux:table.row :key="$answer->id">
                        <flux:table.cell variant="strong" class="w-full">
                            <div class="flex items-center gap-3">
                                <flux:avatar
                                    :name="strtoupper($answer->text)"
                                    size="xs"
                                    color="auto"
                                    initials:single
                                    :color:seed="'answer-'.$answer->id"
                                />
                                <flux:link href="/polls/{{ $poll->id }}/answers/{{ $answer->id }}" wire:navigate>
                                    {{ $answer->text }}
                                </flux:link>
                                <flux:badge color="zinc" size="sm" inset="top bottom" class="tabular-nums">
                                    {{ $this->responseCounts[$answer->id]['count'] }}
                                    {{ Str::plural('response', $this->responseCounts[$answer->id]['count']) }}
                                </flux:badge>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            <div class="flex items-center justify-end gap-3">
                                <div class="mt-1 text-xs tabular-nums">
                                    {{ $this->responseCounts[$answer->id]['percentage'] }}%
                                </div>
                                <div class="h-2 w-36 rounded-full bg-zinc-200">
                                    <div
                                        class="h-2 rounded-full bg-blue-600 transition-all duration-300"
                                        style="width: {{ $this->responseCounts[$answer->id]['percentage'] }}%"
                                    ></div>
                                </div>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @else
        <flux:callout icon="chart-bar" inline>
            <flux:callout.heading>No responses yet</flux:callout.heading>
            <flux:callout.text>
                Share your poll to start collecting responses from your audience. You can embed it in emails or
                newsletters.
            </flux:callout.text>
            <x-slot name="actions">
                <flux:button href="/polls/{{ $poll->id }}/share" variant="primary" color="zinc" size="sm" icon="share" wire:navigate>
                    Share Poll
                </flux:button>
            </x-slot>
        </flux:callout>
    @endif
</div>
