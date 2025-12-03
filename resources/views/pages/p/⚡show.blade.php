<?php

use Livewire\Component;
use App\Models\Poll;
use App\Models\PollResponse;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;

new #[Layout('layouts::simple')] class extends Component {
    public Poll $poll;
    public ?int $selectedAnswer = null;
    public bool $submitted = false;

    protected function rules(): array
    {
        return [
            'selectedAnswer' => [
                'required',
                'integer',
                Rule::exists('answers', 'id')->where(function ($query) {
                    $query->where('poll_id', $this->poll->id);
                }),
            ],
        ];
    }

    public function submit(): void
    {
        $this->validate();

        PollResponse::create([
            'poll_id' => $this->poll->id,
            'answer_id' => $this->selectedAnswer,
        ]);

        $this->submitted = true;
    }
};
?>

<div class="mx-auto max-w-[512px] h-full flex flex-col items-center justify-center">
    @if ($submitted)
        <div class="text-center">
            <flux:heading size="lg">Thank you for your response!</flux:heading>
            <flux:text class="mt-2">Your answer has been recorded successfully.</flux:text>
        </div>
    @else
        <div>
            <flux:heading size="lg">{{ $poll->question }}</flux:heading>

            <form wire:submit="submit" class="mt-8 space-y-6">
                <flux:radio.group
                    wire:model="selectedAnswer"
                    label="Select your answer"
                >
                    @foreach ($poll->answers as $answer)
                        <flux:radio
                            value="{{ $answer->id }}"
                            label="{{ $answer->text }}"
                        />
                    @endforeach
                </flux:radio.group>

                <flux:button type="submit" variant="primary" class="w-full">Submit Response</flux:button>
            </form>
        </div>
    @endif
</div>
