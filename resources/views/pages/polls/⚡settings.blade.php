<?php

use App\Models\Poll;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public Poll $poll;

    public bool $accepts_responses;

    public bool $require_email;

    public bool $auto_submit;

    public bool $collect_feedback;

    public ?string $thank_you_message = null;

    public ?string $thank_you_button_label = null;

    public ?string $thank_you_button_url = null;

    public ?string $redirect_url = null;

    public string $submission_action = 'message';

    public bool $hide_branding;

    public function mount(Poll $poll): void
    {
        $this->authorize('update', $poll);
        $this->poll = $poll;

        $this->accepts_responses = $poll->accepts_responses ?? true;
        $this->require_email = $poll->require_email ?? false;
        $this->auto_submit = $poll->auto_submit ?? false;
        $this->collect_feedback = $poll->collect_feedback ?? false;
        $this->thank_you_message = $poll->thank_you_message;
        $this->thank_you_button_label = $poll->thank_you_button_label;
        $this->thank_you_button_url = $poll->thank_you_button_url;
        $this->redirect_url = $poll->redirect_url;
        $this->submission_action = $poll->submissionAction();
        $this->hide_branding = $poll->hide_branding ?? false;
    }

    public function rules(): array
    {
        $rules = [
            'submission_action' => 'required|in:message,redirect',
        ];

        if ($this->submission_action === 'redirect') {
            $rules['redirect_url'] = 'required|url';
        }

        if ($this->submission_action === 'message' && $this->thank_you_button_label) {
            $rules['thank_you_button_url'] = 'required|url';
        }

        return $rules;
    }

    private function clearConflictingValues(): void
    {
        if ($this->submission_action === 'message') {
            $this->reset(['redirect_url']);

            return;
        }

        $this->reset([
            'thank_you_message',
            'thank_you_button_label',
            'thank_you_button_url',
        ]);

        return;
    }

    public function save(): void
    {
        $this->clearConflictingValues();

        $this->validate();

        $this->poll->update([
            'accepts_responses' => $this->accepts_responses,
            'require_email' => $this->require_email,
            'auto_submit' => $this->auto_submit,
            'collect_feedback' => $this->collect_feedback,
            'thank_you_message' => $this->thank_you_message,
            'thank_you_button_label' => $this->thank_you_button_label,
            'thank_you_button_url' => $this->thank_you_button_url,
            'redirect_url' => $this->redirect_url,
            'hide_branding' => $this->hide_branding,
        ]);

        $this->redirect("/polls/{$this->poll->id}", navigate: true);
    }
}; ?>

<div class="mx-auto max-w-2xl">
    <div class="mb-8">
        <flux:heading size="xl">Poll Settings</flux:heading>
        <flux:text class="mt-2">Configure settings for "{{ $poll->name }}"</flux:text>
    </div>

    <form wire:submit="save" class="space-y-6">
        <flux:fieldset>
            <flux:legend>Response Settings</flux:legend>
            <div class="space-y-4">
                <flux:field variant="inline">
                    <flux:checkbox wire:model="accepts_responses" />
                    <flux:label>Accept responses</flux:label>
                    <flux:description>When disabled, poll will not accept new responses.</flux:description>
                </flux:field>

                <flux:field variant="inline">
                    <flux:checkbox wire:model="require_email" />
                    <flux:label>Require email address</flux:label>
                    <flux:description>Require respondents to provide their email address.</flux:description>
                </flux:field>

                <flux:field variant="inline">
                    <flux:checkbox wire:model="auto_submit" />
                    <flux:label>Auto-submit responses</flux:label>
                    <flux:description>Automatically submit responses when an answer is selected.</flux:description>
                </flux:field>

                <flux:field variant="inline">
                    <flux:checkbox wire:model="collect_feedback" />
                    <flux:label>Collect feedback</flux:label>
                    <flux:description>Show a feedback field after response submission.</flux:description>
                </flux:field>
            </div>
        </flux:fieldset>

        <flux:fieldset>
            <flux:legend>After Submission</flux:legend>
            <div class="space-y-6">
                <flux:radio.group
                    wire:model="submission_action"
                    label="What should happen after submission?"
                    variant="cards"
                    class="max-sm:flex-col"
                >
                    <flux:radio
                        value="message"
                        label="Show thank you message"
                        description="Display a custom thank you message with optional button"
                    />
                    <flux:radio
                        value="redirect"
                        label="Redirect to URL"
                        description="Automatically redirect users to a custom URL after submission"
                    />
                </flux:radio.group>

                <div x-cloak x-show="$wire.submission_action === 'message'" class="space-y-4">
                    <flux:field>
                        <flux:label badge="Optional">Custom thank you message</flux:label>
                        <flux:textarea
                            wire:model="thank_you_message"
                            placeholder="Thank you for your response!"
                            rows="3"
                        />
                        <flux:description>
                            Custom message to show after submission. Leave empty for default message.
                        </flux:description>
                        <flux:error name="thank_you_message" />
                    </flux:field>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <flux:field>
                            <flux:label badge="Optional">Button label</flux:label>
                            <flux:input wire:model="thank_you_button_label" placeholder="Continue" />
                            <flux:error name="thank_you_button_label" />
                        </flux:field>
                        <flux:field>
                            <flux:label badge="Optional">Button URL</flux:label>
                            <flux:input
                                wire:model="thank_you_button_url"
                                type="url"
                                placeholder="https://example.com"
                            />
                            <flux:error name="thank_you_button_url" />
                        </flux:field>
                    </div>
                    <flux:text class="text-sm">Show a custom button on the thank you screen.</flux:text>
                </div>

                <div x-cloak x-show="$wire.submission_action === 'redirect'">
                    <flux:input
                        wire:model="redirect_url"
                        type="url"
                        label="Redirect URL"
                        placeholder="https://example.com/success"
                        description="Users will be redirected to this URL after submitting their response."
                    />
                </div>
            </div>
        </flux:fieldset>

        <flux:fieldset>
            <flux:legend>Branding</flux:legend>
            <flux:field variant="inline">
                <flux:checkbox wire:model="hide_branding" />
                <flux:label>Hide Antipoll branding</flux:label>
                <flux:description>Remove Antipoll branding from the poll display.</flux:description>
            </flux:field>
        </flux:fieldset>

        <div class="flex justify-end">
            <flux:button type="submit" variant="primary">Save Settings</flux:button>
        </div>
    </form>
</div>
