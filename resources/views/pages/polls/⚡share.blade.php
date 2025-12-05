<?php

use App\Models\Poll;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
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
        $question = e($this->poll->question);
        $layout = $this->poll->layout;

        $answerLinks = $this->poll->answers->map(function ($answer) {
            return "<a href=\"{$this->generateAnswerUrl($answer)}\">".e($answer->text).'</a>';
        });

        if ($layout === 'horizontal') {
            return <<<HTML
                <h2>{$question}</h2>
                <p>{$answerLinks->implode(' | ')}</p>
                HTML;
        }

        $listItems = $answerLinks->map(fn ($link) => "    <li>{$link}</li>")->implode("\n");

        return <<<HTML
            <h2>{$question}</h2>

            <ul>

            {$listItems}</ul>
            HTML;
    }

    private function generateAnswerUrl($answer): string
    {
        $url = url("/p/{$this->poll->ulid}?answer={$answer->ulid}");

        return match ($this->platform) {
            'beehiiv' => $url.'&email={{email}}',
            'brevo' => $url.'&email={{contact.EMAIL}}',
            'emailoctopus' => $url.'&email={{EmailAddress}}',
            'ghost' => $url.'&email={email}',
            'hubspot' => $url.'&email={{personalization_token(\'contact.email\',\'\')}}',
            'kit' => $url.'&email={{ subscriber.email_address }}',
            'loops' => $url.'&email={email}',
            'mailerlite' => $url.'&email={email}',
            'sendy' => $url.'&email=[Email]',
            default => $url,
        };
    }
}; ?>

<div class="mx-auto w-full max-w-[512px]">
    <flux:link href="/polls/{{ $poll->id }}" class="inline-flex items-center gap-2 text-sm" variant="subtle" inline wire:navigate>
        <flux:icon.chevron-left variant="micro" />
        Back to poll
    </flux:link>

    <flux:spacer class="mt-4 lg:mt-8" />

    <flux:heading class="text-xl">Share Poll</flux:heading>
    <flux:text class="mt-2">
        Select a platform and copy the HTML to share this poll via email or other platforms.
    </flux:text>

    <flux:spacer class="mt-10" />

    <div class="space-y-6">
        <flux:field>
            <flux:label>Platform</flux:label>
            <flux:radio.group wire:model.live="platform" variant="cards" class="flex-col">
                <flux:radio
                    value="universal"
                    label="Universal"
                    description="Works universally with Apple Mail, Gmail, Substack and more, but it doesn't automatically link responses to subscribers or contacts."
                />
                <flux:radio
                    value="beehiiv"
                    label="Beehiiv"
                    description="Automatically links responses to subscribers or contacts."
                />
                <flux:radio
                    value="brevo"
                    label="Brevo"
                    description="Automatically links responses to subscribers or contacts."
                />
                <flux:radio
                    value="emailoctopus"
                    label="EmailOctopus"
                    description="Automatically links responses to subscribers or contacts."
                />
                <flux:radio
                    value="ghost"
                    label="Ghost"
                    description="For email posts, it links responses to Ghost members. The poll must be embedded as an email call-to-action content card. For regular blog posts, users must use the universal poll instead."
                />
                <flux:radio
                    value="hubspot"
                    label="HubSpot"
                    description="It requires the 'Marketing Hub Starter' plan or higher."
                />
                <flux:radio
                    value="kit"
                    label="Kit"
                    description="Automatically links responses to subscribers or contacts."
                />
                <flux:radio
                    value="loops"
                    label="Loops"
                    description="Automatically links responses to subscribers or contacts."
                />
                <flux:radio
                    value="mailerlite"
                    label="MailerLite"
                    description="Automatically links responses to subscribers or contacts."
                />
                <flux:radio
                    value="sendy"
                    label="Sendy"
                    description="Automatically links responses to subscribers or contacts."
                />
            </flux:radio.group>
        </flux:field>

        <div>
            <flux:heading>Preview</flux:heading>

            <flux:card class="mt-3 p-4">
                <flux:text class="font-semibold" variant="strong">{{ $poll->question }}</flux:text>

                <flux:spacer class="mt-3" />

                @if ($poll->layout === 'horizontal')
                    <div class="flex flex-wrap gap-2">
                        @foreach ($poll->answers as $answer)
                            <flux:text inline>
                                {{ $answer->text }}
                            </flux:text>
                        @endforeach
                    </div>
                @else
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($poll->answers as $answer)
                            <li>
                                <flux:text inline variant="strong">{{ $answer->text }}</flux:text>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </flux:card>
        </div>
    </div>

    <flux:spacer class="mt-8" />

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

                setTimeout(() => {
                    button.textContent = originalText
                }, 2000)
            },
        }"
        x-on:click="copyToClipboard()"
        variant="primary"
        color="zinc"
        class="w-full"
    >
        Copy to Clipboard
    </flux:button>
</div>
