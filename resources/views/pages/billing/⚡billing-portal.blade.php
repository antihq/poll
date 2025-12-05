<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Billing portal')] class extends Component {
    public function mount()
    {
        $team = Auth::user()->currentTeam;

        return $this->redirect($team->billingPortalUrl(url('/dashboard')), navigate: false);
    }
}; ?>

<div>
    <!-- // -->
</div>
