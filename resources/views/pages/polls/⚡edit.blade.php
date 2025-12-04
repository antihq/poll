<?php

use App\Models\Answer;
use App\Models\Poll;
use Livewire\Component;

new class extends Component
{
    public Poll $poll;

    public string $name = '';

    public string $question = '';

    public array $answers = [];

    public string $layout = 'vertical';

    protected array $rules = [
        'name' => ['required', 'string', 'max:255'],
        'question' => ['required', 'string', 'max:1000'],
        'layout' => ['required', 'string', 'in:vertical,horizontal'],
        'answers' => ['required', 'array', 'min:2'],
        'answers.*' => ['required', 'string', 'max:255'],
    ];

    public function mount(Poll $poll): void
    {
        $this->poll = $poll;
        $this->authorize('update', $poll);

        $this->name = $poll->name;
        $this->question = $poll->question;
        $this->layout = $poll->layout ?? 'vertical';
        $this->answers = $poll->answers->map(fn ($answer) => $answer->text)->toArray();
    }

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

    public function update(): void
    {
        $this->validate();

        $this->poll->update([
            'name' => $this->name,
            'question' => $this->question,
            'layout' => $this->layout,
        ]);

        $existingAnswers = $this->poll->answers;
        $answerTexts = array_filter($this->answers, fn ($answer) => trim($answer) !== '');

        foreach ($answerTexts as $index => $answerText) {
            $trimmedText = trim($answerText);

            if ($existingAnswers->has($index)) {
                $existingAnswers[$index]->update([
                    'text' => $trimmedText,
                    'sort_order' => $index,
                ]);
            } else {
                Answer::create([
                    'poll_id' => $this->poll->id,
                    'text' => $trimmedText,
                    'sort_order' => $index,
                ]);
            }
        }

        if ($existingAnswers->count() > count($answerTexts)) {
            $existingAnswers->slice(count($answerTexts))->each->delete();
        }

        $this->redirect("/polls/{$this->poll->id}", navigate: true);
    }
}; ?>

<div class="mx-auto max-w-[512px]">
    <flux:link
        href="/polls/{{ $poll->id }}"
        class="inline-flex items-center gap-2 text-sm"
        variant="subtle"
        inline
        wire:navigate
    >
        <flux:icon.chevron-left variant="micro" />
        Back to poll
    </flux:link>

    <flux:spacer class="mt-4 lg:mt-8" />

    <form wire:submit="update">
        <flux:heading class="text-xl">Edit poll</flux:heading>
        <flux:text class="mt-2">Update your poll details and answers.</flux:text>

        <flux:spacer class="mt-10" />

        <div class="space-y-6">
            <flux:input label="Poll name" placeholder="Team Satisfaction Survey" wire:model="name" />

            <flux:textarea
                label="Question"
                placeholder="How satisfied are you with our team collaboration?"
                wire:model="question"
            />

            <flux:select label="Layout" wire:model="layout">
                <option value="vertical">Vertical</option>
                <option value="horizontal">Horizontal</option>
            </flux:select>

            <div>
                <flux:field>
                    <flux:label>Answers</flux:label>
                    <flux:text class="text-sm text-gray-600">Add at least 2 answers for your poll</flux:text>

                    @foreach ($answers as $index => $answer)
                        <div class="mt-2 flex items-center gap-2">
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
                        </div>
                    @endforeach

                    <flux:button type="button" size="sm" wire:click="addAnswer" class="mt-3" icon="plus">
                        Add answer
                    </flux:button>
                </flux:field>
            </div>
        </div>

        <flux:spacer class="mt-8" />

        <div class="flex flex-col gap-4">
            <flux:button type="submit" variant="primary" color="zinc" class="w-full">Save changes</flux:button>
        </div>
    </form>
</div>
