<?php

use App\Models\Poll;
use App\Models\PollResponse;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Layout('layouts::simple')] class extends Component
{
    public Poll $poll;

    public bool $submitted = false;

    #[Url]
    public ?string $answer = null;

    #[Url]
    public ?string $email = null;

    public ?string $feedback = null;

    #[Computed]
    public function selectedAnswer()
    {
        return $this->poll->answers()->where('ulid', $this->answer)->first();
    }

    protected function rules(): array
    {
        $rules = [
            'answer' => [
                'required',
                'string',
                Rule::exists('answers', 'ulid')->where(function ($query) {
                    $query->where('poll_id', $this->poll->id);
                }),
            ],
            'email' => ['nullable', 'email', 'max:255'],
        ];

        $selectedAnswer = $this->selectedAnswer;
        if ($selectedAnswer && ! empty($selectedAnswer->feedback_field_label)) {
            $rules['feedback'] = ['required', 'string', 'max:1000'];
        }

        return $rules;
    }

    public function submit(): void
    {
        $this->validate();

        $answer = $this->selectedAnswer;

        PollResponse::create([
            'poll_id' => $this->poll->id,
            'answer_id' => $answer->id,
            'email' => $this->email,
            'feedback' => $this->feedback,
        ]);

        if ($answer->redirect_url) {
            $this->redirect($answer->redirect_url);

            return;
        }

        $this->submitted = true;
    }
};
?>

<div class="mx-auto flex h-full max-w-[512px] flex-col items-center justify-center">
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
                    wire:model.live="answer"
                    variant="cards"
                    label="Select your answer"
                    @class([
                        'max-sm:flex-col' => $poll->layout === 'horizontal',
                        'flex-col' => $poll->layout === 'vertical',
                    ])
                >
                    @foreach ($poll->answers as $answer)
                        <flux:radio :value="$answer->ulid" :label="$answer->text" />
                    @endforeach
                </flux:radio.group>

                @if ($this->selectedAnswer && ! empty($this->selectedAnswer->feedback_field_label))
                    <flux:textarea
                        wire:model="feedback"
                        :label="$this->selectedAnswer->feedback_field_label"
                        placeholder="Please provide your feedback..."
                    />
                @endif

                <flux:button type="submit" variant="primary" class="w-full">Submit Response</flux:button>
            </form>
        </div>
    @endif
</div>
