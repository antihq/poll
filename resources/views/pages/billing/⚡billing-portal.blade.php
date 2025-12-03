<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component {
    public function mount()
    {
        $user = Auth::user();

        return $this->redirect($user->billingPortalUrl('/dashboard'), navigate: false);
    }
}; ?>

<div>
    <!-- // -->
</div>
