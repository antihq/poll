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
            'email' => [$this->poll->require_email ? 'required' : 'nullable', 'email', 'max:255'],
        ];

        $selectedAnswer = $this->selectedAnswer;
        if (($selectedAnswer && ! empty($selectedAnswer->feedback_field_label)) || $this->poll->collect_feedback) {
            $rules['feedback'] = ['required', 'string', 'max:1000'];
        }

        return $rules;
    }

    public function mount(Poll $poll): void
    {
        if (! $poll->accepts_responses) {
            abort(404);
        }

        $this->poll = $poll;
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

        if ($this->poll->redirect_url) {
            $this->redirect($this->poll->redirect_url);

            return;
        }

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
            <flux:heading size="lg">{{ $poll->thank_you_message ?? 'Thank you for your response!' }}</flux:heading>
            <flux:text class="mt-2">Your answer has been recorded successfully.</flux:text>

            @if ($poll->thank_you_button_label && $poll->thank_you_button_url)
                <div class="mt-6">
                    <flux:button
                        variant="primary"
                        onclick="window.location.href = '{{ $poll->thank_you_button_url }}'"
                    >
                        {{ $poll->thank_you_button_label }}
                    </flux:button>
                </div>
            @endif
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
                    wire:change="$poll->auto_submit ? submit() : null"
                >
                    @foreach ($poll->answers as $answer)
                        <flux:radio :value="$answer->ulid" :label="$answer->text" />
                    @endforeach
                </flux:radio.group>

                @if ($poll->require_email)
                    <flux:input
                        wire:model="email"
                        type="email"
                        label="Email address"
                        placeholder="your@email.com"
                        required
                    />
                @endif

                @if (($this->selectedAnswer && ! empty($this->selectedAnswer->feedback_field_label)) || $poll->collect_feedback)
                    <flux:textarea
                        wire:model="feedback"
                        :label="$this->selectedAnswer->feedback_field_label ?? 'Feedback'"
                        placeholder="Please provide your feedback..."
                        required
                    />
                @endif

                @if (! $poll->auto_submit)
                    <flux:button type="submit" variant="primary" class="w-full">Submit Response</flux:button>
                @endif
            </form>
        </div>
    @endif

    @if (! $poll->hide_branding)
        <div class="mt-8 text-center">
            <flux:text size="sm" class="text-gray-500">
                Powered by
                <flux:link href="https://antipoll.com" target="_blank">Antipoll</flux:link>
            </flux:text>
        </div>
    @endif
</div>
