<?php

use Livewire\Component;
use App\Models\Poll;
use Livewire\Attributes\Computed;
use Illuminate\Support\Str;

new class extends Component {
    public Poll $poll;

    public function mount(Poll $poll): void
    {
        $this->poll = $poll->load(['answers' => function ($query) {
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
                ]
            ];
        })->all();
    }
}; ?>

<div class="mx-auto max-w-3xl">
    <flux:heading size="lg">{{ $poll->name }}</flux:heading>
    <flux:text class="mt-2">{{ $poll->question }}</flux:text>
    
    <div class="mt-4">
        <flux:button variant="ghost" href="/p/{{ $poll->ulid }}">
            Share Poll
        </flux:button>
    </div>
    
    <flux:subheading class="mt-6">
        {{ $this->totalResponses }} {{ Str::plural('response', $this->totalResponses) }}
    </flux:subheading>

    <flux:table class="mt-6">
        <flux:table.columns>
            <flux:table.column>Answer</flux:table.column>
            <flux:table.column>Responses</flux:table.column>
            <flux:table.column>Progress</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach ($poll->answers as $answer)
                <flux:table.row :key="$answer->id">
                    <flux:table.cell variant="strong">{{ $answer->text }}</flux:table.cell>
                    <flux:table.cell>
                        {{ $this->responseCounts[$answer->id]['count'] }} {{ Str::plural('response', $this->responseCounts[$answer->id]['count']) }} ({{ $this->responseCounts[$answer->id]['percentage'] }}%)
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div 
                                class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                style="width: {{ $this->responseCounts[$answer->id]['percentage'] }}%;"
                            ></div>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
    
    @if ($poll->answers->isEmpty())
        <flux:callout class="mt-6" variant="subtle">
            <flux:text>This poll has no answers yet.</flux:text>
        </flux:callout>
    @endif
</div>