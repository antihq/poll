<?php

use App\Models\Poll;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Poll settings')] class extends Component
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

    public string $layout;

    public function mount(): void
    {
        $this->authorize('update', $this->poll);

        $this->fill([
            'accepts_responses' => $this->poll->accepts_responses ?? true,
            'require_email' => $this->poll->require_email ?? false,
            'auto_submit' => $this->poll->auto_submit ?? false,
            'collect_feedback' => $this->poll->collect_feedback ?? false,
            'thank_you_message' => $this->poll->thank_you_message,
            'thank_you_button_label' => $this->poll->thank_you_button_label,
            'thank_you_button_url' => $this->poll->thank_you_button_url,
            'redirect_url' => $this->poll->redirect_url,
            'submission_action' => $this->poll->submissionAction(),
            'hide_branding' => $this->poll->hide_branding ?? false,
            'layout' => $this->poll->layout ?? 'vertical',
        ]);
    }

    public function rules(): array
    {
        $rules = [
            'submission_action' => 'required|in:message,redirect',
            'layout' => 'required|string|in:vertical,horizontal',
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
            'layout' => $this->layout,
        ]);

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

    <form wire:submit="save">
        <header class="flex items-center gap-3">
            <flux:heading class="text-xl">Poll settings</flux:heading>
            <span class="size-1 rounded-full bg-zinc-500 dark:text-white/70"></span>
            <flux:text class="text-xl">{{ $poll->name }}</flux:text>
        </header>
        <flux:text class="mt-2">Configure settings for this poll.</flux:text>

        <flux:spacer class="mt-10" />

        <div class="space-y-6">
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
                <flux:legend>Display Settings</flux:legend>
                <flux:radio.group label="Layout" wire:model="layout" variant="cards" class="max-sm:flex-col">
                    <flux:radio value="vertical" label="Vertical" description="Answers stacked vertically" />
                    <flux:radio value="horizontal" label="Horizontal" description="Answers arranged horizontally" />
                </flux:radio.group>
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
                    <flux:label>Hide AntiPoll branding</flux:label>
                    <flux:description>Remove AntiPoll branding from the poll display.</flux:description>
                </flux:field>
            </flux:fieldset>
        </div>

        <flux:spacer class="mt-8" />

        <flux:button type="submit" variant="primary" color="zinc" class="w-full">Save Settings</flux:button>
    </form>
</div>
