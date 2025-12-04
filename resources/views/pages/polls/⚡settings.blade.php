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
        <h1 class="text-3xl font-bold text-gray-900">Poll Settings</h1>
        <p class="mt-2 text-gray-600">Configure settings for "{{ $poll->name }}"</p>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 rounded-md border border-green-200 bg-green-50 p-4">
            <p class="text-green-800">{{ session('message') }}</p>
        </div>
    @endif

    <form wire:submit="save" class="space-y-6">
        <div class="rounded-lg bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-medium text-gray-900">Response Settings</h2>

            <div class="space-y-4">
                <div>
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            wire:model="accepts_responses"
                            class="focus:ring-opacity-50 rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                        />
                        <span class="ml-2 text-sm font-medium text-gray-700">Accept responses</span>
                    </label>
                    <p class="mt-1 text-sm text-gray-500">When disabled, the poll will not accept new responses.</p>
                </div>

                <div>
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            wire:model="require_email"
                            class="focus:ring-opacity-50 rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                        />
                        <span class="ml-2 text-sm font-medium text-gray-700">Require email address</span>
                    </label>
                    <p class="mt-1 text-sm text-gray-500">Require respondents to provide their email address.</p>
                </div>

                <div>
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            wire:model="auto_submit"
                            class="focus:ring-opacity-50 rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                        />
                        <span class="ml-2 text-sm font-medium text-gray-700">Auto-submit responses</span>
                    </label>
                    <p class="mt-1 text-sm text-gray-500">Automatically submit responses when an answer is selected.</p>
                </div>

                <div>
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            wire:model="collect_feedback"
                            class="focus:ring-opacity-50 rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                        />
                        <span class="ml-2 text-sm font-medium text-gray-700">Collect feedback</span>
                    </label>
                    <p class="mt-1 text-sm text-gray-500">Show a feedback field after response submission.</p>
                </div>
            </div>
        </div>

        <div class="rounded-lg bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-medium text-gray-900">Thank You Settings</h2>

            <div class="space-y-4">
                <div>
                    <label for="thank_you_message" class="block text-sm font-medium text-gray-700">
                        Custom thank you message
                    </label>
                    <textarea
                        id="thank_you_message"
                        wire:model="thank_you_message"
                        rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Thank you for your response!"
                    ></textarea>
                    <p class="mt-1 text-sm text-gray-500">
                        Custom message to show after submission. Leave empty for default message.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="thank_you_button_label" class="block text-sm font-medium text-gray-700">
                            Button label
                        </label>
                        <input
                            type="text"
                            id="thank_you_button_label"
                            wire:model="thank_you_button_label"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Continue"
                        />
                    </div>
                    <div>
                        <label for="thank_you_button_url" class="block text-sm font-medium text-gray-700">
                            Button URL
                        </label>
                        <input
                            type="url"
                            id="thank_you_button_url"
                            wire:model="thank_you_button_url"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="https://example.com"
                        />
                    </div>
                </div>
                <p class="text-sm text-gray-500">Show a custom button on the thank you screen.</p>

                <div>
                    <label for="redirect_url" class="block text-sm font-medium text-gray-700">Redirect URL</label>
                    <input
                        type="url"
                        id="redirect_url"
                        wire:model="redirect_url"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="https://example.com/success"
                    />
                    <p class="mt-1 text-sm text-gray-500">
                        Redirect to this URL instead of showing a thank you message.
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-lg bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-medium text-gray-900">Branding</h2>

            <div>
                <label class="flex items-center">
                    <input
                        type="checkbox"
                        wire:model="hide_branding"
                        class="focus:ring-opacity-50 rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                    />
                    <span class="ml-2 text-sm font-medium text-gray-700">Hide Antipoll branding</span>
                </label>
                <p class="mt-1 text-sm text-gray-500">Remove Antipoll branding from the poll display.</p>
            </div>
        </div>

        <div class="flex justify-end">
            <button
                type="submit"
                class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none"
            >
                Save Settings
            </button>
        </div>
    </form>
</div>
