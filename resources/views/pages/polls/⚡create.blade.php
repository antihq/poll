<?php

use App\Models\Poll;
use Livewire\Component;
use Livewire\Attributes\Validate;

new class extends Component
{
    #[Validate('required|string|max:255')]
    public $question = '';

    #[Validate('array|min:2|max:10')]
    public $answers = [];

    #[Validate('string|in:vertical,horizontal')]
    public $layout = 'vertical';

    #[Validate('boolean')]
    public $autoSubmit = true;

    #[Validate('boolean')]
    public $requireEmail = false;

    #[Validate('boolean')]
    public $collectFeedback = false;

    #[Validate('nullable|string|max:1000')]
    public $thankYouMessage = '';

    #[Validate('boolean')]
    public $hideBranding = false;

    public function mount()
    {
        $this->answers = [
            ['text' => '', 'emoji' => '', 'redirect_url' => null, 'collect_feedback' => false],
            ['text' => '', 'emoji' => '', 'redirect_url' => null, 'collect_feedback' => false],
        ];
    }

    public function createPoll()
    {
        $this->validate();

        $poll = Poll::create([
            'organization_id' => auth()->user()->currentOrganization->id,
            'question' => $this->question,
            'layout_type' => $this->layout,
            'auto_submit' => $this->autoSubmit,
            'require_email' => $this->requireEmail,
            'collect_feedback' => $this->collectFeedback,
            'thank_you_message' => $this->thankYouMessage,
            'hide_branding' => $this->hideBranding,
            'status' => 'draft',
        ]);

        foreach ($this->answers as $index => $answer) {
            if (empty($answer['text']) && empty($answer['emoji'])) {
                continue;
            }

            $poll->options()->create([
                'answer_text' => $answer['text'],
                'answer_emoji' => $answer['emoji'],
                'redirect_url' => $answer['redirect_url'] ?? null,
                'collect_feedback' => $answer['collect_feedback'] ?? false,
                'sort_order' => $index,
            ]);
        }

        return redirect()->route('polls.index')->with('success', 'Poll created successfully!');
    }

    public function addAnswer()
    {
        if (count($this->answers) < 10) {
            $this->answers[] = ['text' => '', 'emoji' => '', 'redirect_url' => null, 'collect_feedback' => false];
        }
    }

    public function removeAnswer($index)
    {
        if (count($this->answers) > 2) {
            array_splice($this->answers, $index, 1);
        }
    }
};
?>

<div>
    <h1>Create Poll</h1>
    
    <form wire:submit="createPoll">
        <!-- Question -->
        <div>
            <label for="question">Question</label>
            <input type="text" id="question" wire:model="question">
            @error('question') <span>{{ $message }}</span> @enderror
        </div>

        <!-- Answers -->
        <div>
            <label>Answer Options</label>
            @foreach($answers as $index => $answer)
                <div>
                    <input type="text" wire:model="answers.{{ $index }}.text" placeholder="Answer text">
                    <input type="text" wire:model="answers.{{ $index }}.emoji" placeholder="Emoji">
                    <button type="button" wire:click="removeAnswer({{ $index }})">Remove</button>
                </div>
            @endforeach
            
            @if(count($answers) < 10)
                <button type="button" wire:click="addAnswer">Add Answer</button>
            @endif
        </div>

        <!-- Layout -->
        <div>
            <label>Layout</label>
            <select wire:model="layout">
                <option value="vertical">Vertical</option>
                <option value="horizontal">Horizontal</option>
            </select>
        </div>

        <!-- Auto Submit -->
        <div>
            <label>
                <input type="checkbox" wire:model="autoSubmit">
                Auto-submit responses
            </label>
        </div>

        <!-- Require Email -->
        <div>
            <label>
                <input type="checkbox" wire:model="requireEmail">
                Require email address
            </label>
        </div>

        <!-- Collect Feedback -->
        <div>
            <label>
                <input type="checkbox" wire:model="collectFeedback">
                Collect feedback
            </label>
        </div>

        <!-- Thank You Message -->
        <div>
            <label for="thankYouMessage">Thank You Message</label>
            <textarea id="thankYouMessage" wire:model="thankYouMessage"></textarea>
            @error('thankYouMessage') <span>{{ $message }}</span> @enderror
        </div>

        <!-- Hide Branding -->
        <div>
            <label>
                <input type="checkbox" wire:model="hideBranding">
                Hide branding
            </label>
        </div>

        <button type="submit">Create Poll</button>
    </form>
</div>