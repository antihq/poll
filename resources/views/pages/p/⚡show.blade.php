<?php

use Livewire\Component;
use App\Models\Poll;
use App\Models\PollResponse;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

new #[Layout('layouts::simple')] class extends Component {
    public Poll $poll;
    public ?string $answer = null;
    public bool $submitted = false;

    #[Url]
    public ?string $email = null;

    protected function rules(): array
    {
        return [
            'answer' => [
                'required',
                'string',
                Rule::exists('answers', 'ulid')->where(function ($query) {
                    $query->where('poll_id', $this->poll->id);
                }),
            ],
            'email' => ['nullable', 'email', 'max:255'],
        ];
    }

    public function submit(): void
    {
        $this->validate();

        $answer = $this->poll->answers()->where('ulid', $this->answer)->first();

        PollResponse::create([
            'poll_id' => $this->poll->id,
            'answer_id' => $answer->id,
            'email' => $this->email,
        ]);

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
                <flux:radio.group wire:model="answer" label="Select your answer">
                    @foreach ($poll->answers as $answer)
                        <flux:radio :value="$answer->ulid" :label="$answer->text" />
                    @endforeach
                </flux:radio.group>

                <flux:button type="submit" variant="primary" class="w-full">Submit Response</flux:button>
            </form>
        </div>
    @endif
</div>
