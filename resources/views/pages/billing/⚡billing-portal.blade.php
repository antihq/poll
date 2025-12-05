<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component {
    public function mount()
    {
        $team = Auth::user()->currentTeam;

        return $this->redirect($team->billingPortalUrl('/dashboard'), navigate: false);
    }
}; ?>

<div>
    <!-- // -->
</div>
