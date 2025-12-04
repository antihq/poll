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
    <header class="flex items-center">
        <div class="flex items-center gap-3">
            <flux:avatar
                :name="strtoupper($answer->poll->name)"
                color="auto"
                initials:single
                :color:seed="'poll-'.$answer->poll->id"
            />
            <flux:heading class="text-xl">{{ $answer->poll->name }}</flux:heading>
        </div>
        <flux:spacer />
        <flux:button
            href="/polls/{{ $answer->poll->id }}"
            icon:trailing="arrow-left"
            size="sm"
            variant="subtle"
            wire:navigate
        >
            Back to Poll
        </flux:button>
    </header>

    <flux:spacer class="mt-8" />

    <flux:heading size="lg">Answer Details</flux:heading>

    <flux:card class="mt-4">
        <flux:text class="font-semibold" variant="strong">{{ $answer->text }}</flux:text>
        <flux:text class="mt-2 text-sm text-gray-500">
            {{ $this->responses->count() }} {{ Str::plural('response', $this->responses->count()) }}
        </flux:text>
    </flux:card>

    <flux:spacer class="mt-8" />

    <header class="flex items-center">
        <flux:heading size="lg">
            {{ $this->responses->count() }} {{ Str::plural('Response', $this->responses->count()) }}
        </flux:heading>
    </header>

    <flux:separator class="mt-3" />

    @if ($this->responses->count() > 0)
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
        <div class="py-8 text-center text-gray-500">
            <flux:text>No responses yet for this answer.</flux:text>
        </div>
    @endif
</div>
