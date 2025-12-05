<?php

use App\Models\Answer;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public Answer $answer;

    public function mount(Answer $answer)
    {
        $this->authorize('view', $this->answer->poll);
        $this->answer->load('poll');
    }

    #[Computed]
    public function responses()
    {
        return $this->answer->pollResponses()
            ->orderBy('created_at', 'desc')
            ->get();
    }
};
?>

<div class="mx-auto max-w-3xl">
    <flux:link
        href="/polls/{{ $answer->poll->id }}"
        class="inline-flex items-center gap-2 text-sm"
        variant="subtle"
        inline
        wire:navigate
    >
        <flux:icon.chevron-left variant="micro" />
        Back to poll
    </flux:link>

    <flux:spacer class="mt-4 lg:mt-8" />

    @if ($this->responses->count() > 0)
        <header class="flex items-center gap-3">
            <flux:heading class="text-xl">Answer details</flux:heading>
            <span class="size-1 rounded-full bg-zinc-400"></span>
            <flux:text class="text-xl">{{ $answer->text }}</flux:text>
        </header>

        <flux:spacer class="mt-8" />

        <header class="flex items-center">
            <flux:heading size="lg">
                {{ $this->responses->count() }} {{ Str::plural('Response', $this->responses->count()) }}
            </flux:heading>
        </header>

        <flux:separator class="mt-3" />

        <flux:table>
            <flux:table.rows>
                @foreach ($this->responses as $response)
                    <flux:table.row :key="$response->id">
                        <flux:table.cell variant="strong" class="w-full">
                            <div class="flex items-center gap-3">
                                @if ($response->email)
                                    <flux:avatar
                                        :name="strtoupper($response->email)"
                                        size="xs"
                                        color="auto"
                                        initials:single
                                        :color:seed="'response-'.$response->id"
                                    />
                                    {{ $response->email }}
                                @else
                                    <flux:text>No email provided</flux:text>
                                @endif

                                @if (!empty($response->feedback))
                                    <flux:tooltip toggleable position="right">
                                        <flux:button icon="information-circle" size="xs" variant="ghost" />
                                        <flux:tooltip.content class="max-w-md">
                                            <p>{{ $response->feedback }}</p>
                                        </flux:tooltip.content>
                                    </flux:tooltip>
                                @endif
                            </div>
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            <flux:text class="text-sm text-gray-500">
                                {{ $response->created_at->format('M j, Y g:i A') }}
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @else
        <flux:callout variant="secondary" icon="inbox">
            <flux:callout.heading>No responses yet</flux:callout.heading>
            <flux:callout.text>
                This answer hasn't received any responses yet.
            </flux:callout.text>
        </flux:callout>
    @endif
</div>
