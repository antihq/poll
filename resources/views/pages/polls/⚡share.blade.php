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
        $content = "<h2>{$this->poll->question}</h2>\n\n";
        $content .= "<ul>\n";

        foreach ($this->poll->answers as $answer) {
            $url = url("/p/{$this->poll->ulid}?answer={$answer->ulid}");
            $content .= "    <li><a href=\"{$url}\">{$answer->text}</a></li>\n";
        }

        $content .= "</ul>";

        return $content;
    }

    public function copyToClipboard(): void
    {
        $this->dispatch('share-copied', content: $this->shareContent);
    }
}; ?>

<div class="mx-auto max-w-3xl">
    <header class="flex items-center">
        <div class="flex items-center gap-3">
            <flux:avatar :name="strtoupper($poll->name)" color="auto" initials:single :color:seed="'poll-'.$poll->id" />
            <flux:heading class="text-xl">{{ $poll->name }}</flux:heading>
        </div>
        <flux:spacer />
        <flux:button href="/polls/{{ $poll->id }}" icon:trailing="arrow-left" size="sm" variant="subtle">
            Back to Poll
        </flux:button>
    </header>

    <flux:spacer class="mt-8" />

    <flux:heading size="lg">Share Poll</flux:heading>

    <flux:text class="mt-2">
        Select a platform and copy the HTML to share this poll via email or other platforms.
    </flux:text>

    <flux:spacer class="mt-6" />

    <flux:card>
        <flux:heading size="md">Preview</flux:heading>
        <flux:text class="mt-2 font-semibold">{{ $poll->question }}</flux:text>
        <ul class="mt-3 space-y-2">
            @foreach ($poll->answers as $answer)
                <li class="flex items-center gap-2">
                    <flux:icon name="link" size="sm" />
                    <span>{{ $answer->text }}</span>
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

    <flux:field>
        <flux:label>Share Content</flux:label>
        <flux:textarea readonly rows="10" wire:model="shareContent" placeholder="Share content will appear here..." />
    </flux:field>

    <flux:spacer class="mt-4" />

    <flux:button wire:click="copyToClipboard" icon:trailing="clipboard">Copy to Clipboard</flux:button>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('share-copied', ({ content }) => {
                navigator.clipboard.writeText(content).then(() => {
                    // Show success message
                    const button = document.querySelector('[wire\\:click="copyToClipboard"]');
                    const originalText = button.textContent;
                    button.textContent = 'Copied!';
                    button.classList.add('bg-green-600', 'text-white');

                    setTimeout(() => {
                        button.textContent = originalText;
                        button.classList.remove('bg-green-600', 'text-white');
                    }, 2000);
                });
            });
        });
    </script>
</div>
