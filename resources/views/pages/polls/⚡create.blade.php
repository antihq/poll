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

<div class="mx-auto max-w-3xl">
    <flux:heading size="2xl">Create Poll</flux:heading>
    <flux:text class="mt-2">Create a new poll for your organization</flux:text>
    
    <form wire:submit="createPoll" class="mt-8 space-y-8">
        <!-- Question -->
        <flux:input 
            wire:model="question" 
            label="Question" 
            type="text" 
            required 
            placeholder="What would you like to ask?"
            description="The main question for your poll"
        />

        <!-- Answer Options -->
        <div>
            <flux:heading size="lg" class="mb-4">Answer Options</flux:heading>
            
            <div class="space-y-4">
                @foreach($answers as $index => $answer)
                    <flux:card class="p-4">
                        <div class="flex gap-4 items-start">
                            <div class="flex-1 space-y-4">
                                <flux:input 
                                    wire:model="answers.{{ $index }}.text" 
                                    label="Answer Text" 
                                    placeholder="Enter answer text"
                                />
                                
                                <flux:input 
                                    wire:model="answers.{{ $index }}.emoji" 
                                    label="Emoji (optional)" 
                                    placeholder="🎉"
                                    description="Add an emoji to make answers more engaging"
                                />
                                
                                <flux:input 
                                    wire:model="answers.{{ $index }}.redirect_url" 
                                    label="Redirect URL (optional)" 
                                    type="url"
                                    placeholder="https://example.com"
                                    description="URL to redirect to when this answer is selected"
                                />
                                
                                <flux:switch 
                                    wire:model="answers.{{ $index }}.collect_feedback"
                                    label="Collect feedback for this answer"
                                    description="Show a feedback text area when this answer is selected"
                                />
                            </div>
                            
                            @if(count($answers) > 2)
                                <flux:button 
                                    type="button" 
                                    wire:click="removeAnswer({{ $index }})" 
                                    variant="outline"
                                    size="sm"
                                    class="mt-6"
                                >
                                    Remove
                                </flux:button>
                            @endif
                        </div>
                    </flux:card>
                @endforeach
            </div>
            
            @if(count($answers) < 10)
                <flux:button 
                    type="button" 
                    wire:click="addAnswer" 
                    variant="outline" 
                    class="mt-4 w-full"
                >
                    Add Answer Option
                </flux:button>
            @endif
        </div>

        <!-- Layout -->
        <flux:radio.group wire:model="layout" label="Layout">
            <flux:radio value="vertical" label="Vertical" description="Answers displayed in a vertical list" />
            <flux:radio value="horizontal" label="Horizontal" description="Answers displayed in a horizontal row" />
        </flux:radio.group>

        <!-- Settings -->
        <div>
            <flux:heading size="lg" class="mb-4">Poll Settings</flux:heading>
            
            <div class="space-y-4">
                <flux:switch 
                    wire:model="autoSubmit"
                    label="Auto-submit responses"
                    description="Record response immediately when user clicks an answer"
                />
                
                <flux:switch 
                    wire:model="requireEmail"
                    label="Require email address"
                    description="Ask users for their email before submitting"
                />
                
                <flux:switch 
                    wire:model="collectFeedback"
                    label="Collect feedback"
                    description="Allow users to provide additional feedback"
                />
                
                <flux:switch 
                    wire:model="hideBranding"
                    label="Hide branding"
                    description="Remove Antipoll branding from the poll"
                />
            </div>
        </div>

        <!-- Thank You Message -->
        <flux:textarea 
            wire:model="thankYouMessage" 
            label="Thank You Message (optional)"
            placeholder="Thank you for your feedback!"
            description="Custom message shown after poll submission. Supports Markdown."
            rows="4"
        />

        <!-- Submit Button -->
        <div class="flex items-center gap-4">
            <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                Create Poll
            </flux:button>
            
            <flux:button :href="route('polls.index')" wire:navigate>
                Cancel
            </flux:button>
        </div>
    </form>
</div>
