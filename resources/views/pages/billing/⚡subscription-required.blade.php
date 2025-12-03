<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Actions\Logout;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

new #[Layout('layouts::simple')] class extends Component {
    public Collection $teams;
    public ?int $selectedTeamId;

    #[Computed]
    public function user() {
        return Auth::user();
    }

    public function mount()
    {
        $this->teams = $this->user->allTeams();
        $this->selectedTeamId = $this->user->currentTeam?->id;

        if ($this->user->currentTeam->subscribed('default')) {
            $this->redirect(route('dashboard'), navigate: true);
        }
    }

    public function switchTeam(Team $team)
    {
        $this->authorize('switch', $team);

        $this->user->switchTeam($team);

        if ($team->subscribed('default')) {
            $this->redirect(route('dashboard'), navigate: true);
        }
    }

    public function updatedSelectedTeamId(Team $team)
    {
        $this->switchTeam($team);
    }

    public function goToCheckout()
    {
        $stripePriceId = config('services.stripe.price_id');

        $this->redirect($this->user->currentTeam->newSubscription('default', $stripePriceId)
            ->trialDays(31)
            ->checkout([
                'success_url' => route('settings.profile'),
                'cancel_url' => route('subscription-required'),
            ])->asStripeCheckoutSession()->url, navigate: false);
    }

    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="mx-auto flex h-full max-w-sm flex-col justify-center gap-6">
    <div class="flex justify-center">
        <flux:dropdown position="bottom" align="center">
            <flux:profile :name="$this->user->currentTeam->name" />
            <flux:menu>
                <flux:menu.radio.group wire:model.live="selectedTeamId">
                    @foreach ($teams as $team)
                        <flux:menu.radio :value="$team->id">
                            {{ $team->name }}
                        </flux:menu.radio>
                    @endforeach
                </flux:menu.radio.group>
            </flux:menu>
        </flux:dropdown>
    </div>
    <flux:text class="text-center">
        {{ __('You need to subscribe to our service to continue.') }}
    </flux:text>
    <div class="flex flex-col items-center justify-between space-y-3">
        <flux:button wire:click="goToCheckout" variant="primary" class="w-full">
            {{ __('Proceed to Checkout') }}
        </flux:button>
        <flux:link class="cursor-pointer text-sm" wire:click="logout">
            {{ __('Log out') }}
        </flux:link>
    </div>
</div>
