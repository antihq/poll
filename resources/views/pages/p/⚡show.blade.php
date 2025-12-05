<?php

use App\Models\Poll;
use App\Models\PollResponse;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Layout('layouts::public-poll')] class extends Component
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

        if ($answer->redirect_url) {
            $this->redirect($answer->redirect_url);

            return;
        }

        if ($this->poll->shouldRedirect()) {
            $this->redirect($this->poll->redirect_url);

            return;
        }

        $this->submitted = true;
    }
};
?>

<div
    class="mx-auto flex h-full w-full max-w-[512px] flex-col justify-center"
    @if ($poll->auto_submit)
        x-init="$wire.submit()"
    @endif
>
    <flux:spacer />

    @if ($submitted)
        <div class="text-center">
            @if ($poll->thank_you_message)
                <flux:text>{{ $poll->thank_you_message }}</flux:text>
            @else
                <flux:heading class="text-xl">Thank you for your response!</flux:heading>
                <flux:text class="mt-2">Your answer has been recorded successfully.</flux:text>
            @endif

            @if ($poll->thank_you_button_label && $poll->thank_you_button_url)
                <flux:spacer class="mt-8" />

                <flux:button variant="primary" :href="$poll->thank_you_button_url">
                    {{ $poll->thank_you_button_label }}
                </flux:button>
            @endif
        </div>
    @else
        <div>
            <flux:heading class="text-xl">{{ $poll->question }}</flux:heading>

            <flux:spacer class="mt-10" />

            <form wire:submit="submit" class="space-y-6">
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

                <flux:button type="submit" variant="primary" color="zinc" class="w-full">Submit Response</flux:button>
            </form>
        </div>
    @endif

    @if (! $poll->hide_branding)
        <flux:spacer class="mt-4 lg:mt-8" />

        <flux:text size="sm" class="text-center">
            Powered by
            <flux:link href="https://antipoll.com" target="_blank" :accent="false" wire:navigate>Antipoll</flux:link>
        </flux:text>
    @endif
</div>
