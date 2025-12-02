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
        $counts = [];
        $totalResponses = $this->totalResponses;

        foreach ($this->poll->answers as $answer) {
            $responseCount = $answer->pollResponses()->count();
            $percentage = $this->totalResponses > 0 ? round(($responseCount / $this->totalResponses) * 100) : 0;
            
            $counts[$answer->id] = [
                'count' => $responseCount,
                'percentage' => $percentage,
            ];
        }

        return $counts;
    }
}; ?>

<div class="mx-auto max-w-3xl">
    <flux:heading size="lg">{{ $poll->name }}</flux:heading>
    <flux:text class="mt-2">{{ $poll->question }}</flux:text>
    
    <flux:subheading class="mt-6">
        {{ $this->totalResponses }} {{ Str::plural('response', $this->totalResponses) }}
    </flux:subheading>

    <div class="mt-6 space-y-4">
        @foreach ($poll->answers as $answer)
            <div class="border rounded-lg p-4">
                <div class="flex justify-between items-center mb-2">
                    <flux:text class="font-medium">{{ $answer->text }}</flux:text>
                    <flux:text class="text-sm">{{ $this->responseCounts[$answer->id]['percentage'] }}%</flux:text>
                </div>
                
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div 
                        class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                        style="width: {{ $this->responseCounts[$answer->id]['percentage'] }}%;"
                    ></div>
                </div>
                
                <flux:text class="text-sm text-gray-600 mt-2">
                    {{ $this->responseCounts[$answer->id]['count'] }} {{ Str::plural('response', $this->responseCounts[$answer->id]['count']) }}
                </flux:text>
            </div>
        @endforeach
    </div>
    
    @if ($poll->answers->isEmpty())
        <flux:callout class="mt-6" variant="subtle">
            <flux:text>This poll has no answers yet.</flux:text>
        </flux:callout>
    @endif
</div>