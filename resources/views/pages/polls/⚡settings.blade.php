<?php

use App\Models\Poll;
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
        $this->hide_branding = $poll->hide_branding ?? false;
    }

    public function save(): void
    {
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

        session()->flash('message', 'Poll settings updated successfully!');
    }
}; ?>

<div class="mx-auto max-w-2xl">
    <div class="mb-8">
        <flux:heading size="xl">Poll Settings</flux:heading>
        <flux:text class="mt-2">Configure settings for "{{ $poll->name }}"</flux:text>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 rounded-md border border-green-200 bg-green-50 p-4">
            <flux:text color="green">{{ session('message') }}</flux:text>
        </div>
    @endif

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
            <flux:legend>Thank You Settings</flux:legend>
            <div class="space-y-6">
                <flux:textarea
                    wire:model="thank_you_message"
                    label="Custom thank you message"
                    placeholder="Thank you for your response!"
                    rows="3"
                    description="Custom message to show after submission. Leave empty for default message."
                />

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <flux:input wire:model="thank_you_button_label" label="Button label" placeholder="Continue" />
                    <flux:input
                        wire:model="thank_you_button_url"
                        type="url"
                        label="Button URL"
                        placeholder="https://example.com"
                    />
                </div>
                <flux:text class="text-sm">Show a custom button on the thank you screen.</flux:text>

                <flux:input
                    wire:model="redirect_url"
                    type="url"
                    label="Redirect URL"
                    placeholder="https://example.com/success"
                    description="Redirect to this URL instead of showing a thank you message."
                />
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
            <flux:button type="submit">Save Settings</flux:button>
        </div>
    </form>
</div>
