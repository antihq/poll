<?php

use Livewire\Component;
use App\Models\Poll;
use Livewire\Attributes\Computed;

new class extends Component {
    public Poll $poll;

    public string $platform = 'universal';

    public function mount(): void
    {
        $this->authorize('view', $this->poll);

        $this->poll->load(['answers' => function ($query) {
            $query->orderBy('sort_order');
        }]);
    }

    #[Computed]
    public function shareContent(): string
    {
        $question = $this->poll->question;
        $answers = $this->poll->answers->all();
        $pollUlid = $this->poll->ulid;
        $platform = $this->platform;

        return <<<HTML
            <h2>{$question}</h2>

            <ul>

            HTML . implode('', array_map(function ($answer) use ($pollUlid, $platform) {
                $url = url("/p/{$pollUlid}?answer={$answer->ulid}");
                if ($platform === 'kit') {
                    $url .= "&email={{ subscriber.email_address }}";
                }
                return "    <li><a href=\"{$url}\">{$answer->text}</a></li>\n";
            }, $answers)) . <<<HTML
            </ul>
            HTML;
    }


}; ?>

<div class="mx-auto max-w-3xl">
    <header class="flex items-center">
        <div class="flex items-center gap-3">
            <flux:avatar :name="strtoupper($poll->name)" color="auto" initials:single :color:seed="'poll-'.$poll->id" />
            <flux:heading class="text-xl">{{ $poll->name }}</flux:heading>
        </div>
        <flux:spacer />
        <flux:button href="/polls/{{ $poll->id }}" icon:trailing="arrow-left" size="sm" variant="subtle" wire:navigate>
            Back to Poll
        </flux:button>
    </header>

    <flux:spacer class="mt-8" />

    <flux:heading size="lg">Share Poll</flux:heading>

    <flux:text class="mt-2">
        Select a platform and copy the HTML to share this poll via email or other platforms.
    </flux:text>

    <flux:spacer class="mt-6" />

    <flux:heading>Preview</flux:heading>

    <flux:card class="mt-3">
        <flux:text class="mt-2 font-semibold" variant="strong">{{ $poll->question }}</flux:text>
        <ul class="mt-3 list-inside list-disc space-y-2">
            @foreach ($poll->answers as $answer)
                <li>
                    <flux:text inline variant="strong">{{ $answer->text }}</flux:text>
                </li>
            @endforeach
        </ul>
    </flux:card>

    <flux:spacer class="mt-6" />

    <flux:field>
        <flux:label>Platform</flux:label>
        <flux:radio.group wire:model.live="platform">
            <flux:radio value="universal" label="Universal" />
            <flux:radio value="kit" label="Kit" />
        </flux:radio.group>
    </flux:field>

    <flux:spacer class="mt-6" />

    <flux:button
        x-data="{
            copyToClipboard() {
                const htmlContent = `{{ $this->shareContent }}`
                const plainText = htmlContent
                    .replace(/<[^>]*>/g, '')
                    .replace(/&nbsp;/g, ' ')
                    .trim()

                if (navigator.clipboard && window.ClipboardItem) {
                    const clipboardItem = new ClipboardItem({
                        'text/html': new Blob([htmlContent], { type: 'text/html' }),
                        'text/plain': new Blob([plainText], { type: 'text/plain' }),
                    })
                    navigator.clipboard.write([clipboardItem]).then(() => {
                        this.showSuccess()
                    })
                } else {
                    // Fallback for older browsers
                    navigator.clipboard.writeText(plainText).then(() => {
                        this.showSuccess()
                    })
                }
            },
            showSuccess() {
                const button = this.$el
                const originalText = button.textContent
                button.textContent = 'Copied!'
                button.classList.add('bg-green-600', 'text-white')

                setTimeout(() => {
                    button.textContent = originalText
                    button.classList.remove('bg-green-600', 'text-white')
                }, 2000)
            },
        }"
        x-on:click="copyToClipboard()"
        icon:trailing="clipboard"
    >
        Copy to Clipboard
    </flux:button>
</div>
