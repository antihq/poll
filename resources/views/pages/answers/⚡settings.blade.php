<?php

use App\Models\Answer;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Answer settings')] class extends Component
{
    public Answer $answer;

    public bool $enable_redirect_url = false;

    public string $redirect_url = '';

    public bool $show_feedback_field = false;

    public string $feedback_field_label = '';

    protected array $rules = [
        'enable_redirect_url' => ['boolean'],
        'redirect_url' => ['required_if:enable_redirect_url,true', 'url', 'max:255'],
        'show_feedback_field' => ['boolean'],
        'feedback_field_label' => ['required_if:show_feedback_field,true', 'string', 'max:255'],
    ];

    public function mount(Answer $answer): void
    {
        $this->answer = $answer;
        $this->authorize('update', $answer->poll);

        $this->enable_redirect_url = ! empty($this->answer->redirect_url);
        $this->redirect_url = $this->answer->redirect_url ?? '';
        $this->show_feedback_field = ! empty($this->answer->feedback_field_label);
        $this->feedback_field_label = $this->answer->feedback_field_label ?? '';
    }

    public function update(): void
    {
        $this->validate();

        $this->answer->update([
            'redirect_url' => $this->enable_redirect_url ? ($this->redirect_url ?: null) : null,
            'feedback_field_label' => $this->show_feedback_field ? ($this->feedback_field_label ?: null) : null,
        ]);

        $this->redirect("/polls/{$this->answer->poll_id}/edit", navigate: true);
    }
};
?>

<div class="mx-auto max-w-[512px]">
    <flux:link
        href="/polls/{{ $answer->poll_id }}/edit"
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
        <header class="flex items-center gap-3">
            <flux:heading class="text-xl">Answer settings</flux:heading>
            <span class="size-1 rounded-full bg-zinc-500 dark:text-white/70"></span>
            <flux:text class="text-xl">{{ $answer->text }}</flux:text>
        </header>
        <flux:text class="mt-2">Configure redirect and feedback options.</flux:text>

        <flux:spacer class="mt-10" />

        <div class="space-y-6">
            <flux:checkbox
                wire:model="enable_redirect_url"
                label="Enable redirect URL"
                description="Redirect users to a custom URL after selecting this answer"
            />

            <div x-cloak x-show="$wire.enable_redirect_url">
                <flux:input
                    wire:model="redirect_url"
                    placeholder="https://example.com/thank-you"
                    label="Redirect URL"
                    description="Users will be redirected to this URL after selecting this answer"
                />
            </div>

            <flux:checkbox
                wire:model="show_feedback_field"
                label="Show feedback field"
                description="Display a feedback field when this answer is selected"
            />

            <div x-cloak x-show="$wire.show_feedback_field">
                <flux:input
                    wire:model="feedback_field_label"
                    placeholder="Feedback"
                    label="Feedback field label"
                    description="Custom label for the feedback field"
                />
            </div>
        </div>

        <flux:spacer class="mt-8" />

        <flux:button type="submit" variant="primary" color="zinc" class="w-full">Save Settings</flux:button>
    </form>
</div>
