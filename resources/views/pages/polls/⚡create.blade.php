<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Poll;
use App\Models\Answer;
use Illuminate\Support\Str;

new class extends Component {
    public string $name = '';
    public string $question = '';
    public array $answers = ['', ''];

    protected array $rules = [
        'name' => ['required', 'string', 'max:255'],
        'question' => ['required', 'string', 'max:1000'],
        'answers' => ['required', 'array', 'min:2'],
        'answers.*' => ['required', 'string', 'max:255'],
    ];

    public function addAnswer(): void
    {
        $this->answers[] = '';
    }

    public function removeAnswer(int $index): void
    {
        if (count($this->answers) > 2) {
            array_splice($this->answers, $index, 1);
        }
    }

    public function sortAnswer($item, $position): void
    {
        $movedItem = $this->answers[$item];

        array_splice($this->answers, $item, 1);

        array_splice($this->answers, $position, 0, [$movedItem]);
    }

    public function create(): void
    {
        $this->validate();

        $user = Auth::user();
        $poll = Poll::create([
            'ulid' => Str::ulid(),
            'name' => $this->name,
            'question' => $this->question,
            'team_id' => $user->currentTeam->id,
        ]);

        foreach (array_filter($this->answers, fn ($answer) => trim($answer) !== '') as $index => $answerText) {
            Answer::create([
                'poll_id' => $poll->id,
                'text' => trim($answerText),
                'sort_order' => $index,
            ]);
        }

        $this->redirect("/polls/{$poll->id}", navigate: true);
    }
}; ?>

<div class="mx-auto w-full max-w-[512px]">
    <flux:link href="/polls" class="inline-flex items-center gap-2 text-sm" variant="subtle" inline wire:navigate>
        <flux:icon.chevron-left variant="micro" />
        Polls
    </flux:link>

    <flux:spacer class="mt-4 lg:mt-8" />

    <form wire:submit="create">
        <flux:heading class="text-xl">Add a poll</flux:heading>
        <flux:text class="mt-2">Create a new poll for your team.</flux:text>

        <flux:spacer class="mt-10" />

        <div class="space-y-6">
            <flux:input label="Poll name" placeholder="Team Satisfaction Survey" wire:model="name" />

            <flux:textarea
                label="Question"
                placeholder="How satisfied are you with our team collaboration?"
                wire:model="question"
            />

            <div>
                <flux:field>
                    <flux:label>Answers</flux:label>
                    <flux:text class="text-sm text-gray-600">
                        Add at least 2 answers for your poll. Drag to reorder.
                    </flux:text>

                    <ul wire:sort="sortAnswer">
                        @foreach ($answers as $index => $answer)
                            <li wire:sort:item="{{ $index }}" class="mt-2 flex items-center gap-2">
                                <div wire:sort:handle class="cursor-grab p-1 active:cursor-grabbing">
                                    <flux:icon name="bars-3" variant="micro" class="text-gray-400" />
                                </div>
                                <flux:input
                                    wire:model="answers.{{ $index }}"
                                    placeholder="Answer {{ $index + 1 }}"
                                    class="flex-1"
                                />
                                @if (count($answers) > 2)
                                    <flux:button
                                        type="button"
                                        variant="subtle"
                                        size="sm"
                                        wire:click="removeAnswer({{ $index }})"
                                        class="shrink-0"
                                        square
                                    >
                                        <flux:icon name="trash" variant="mini" />
                                    </flux:button>
                                @endif
                            </li>
                        @endforeach
                    </ul>

                    <flux:button type="button" size="sm" wire:click="addAnswer" class="mt-3" icon="plus">
                        Add answer
                    </flux:button>
                </flux:field>
            </div>
        </div>

        <flux:spacer class="mt-8" />

        <flux:button type="submit" variant="primary" color="zinc" class="w-full">Create poll</flux:button>
    </form>
</div>
