<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Team;

new class extends Component {
    public string $name = '';

    public function create()
    {
        $this->authorize('create', Team::class);

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $team = Auth::user()->teams()->create([
            'name' => $this->name,
        ]);

        Auth::user()->switchTeam($team);

        return $this->redirect('/dashboard', navigate: true);
    }
}; ?>

<flux:modal name="create-team" class="md:w-96">
    <form wire:submit="create" class="space-y-6">
        <div>
            <flux:heading size="lg">Create Team</flux:heading>
            <flux:text class="mt-2">Enter a name for your new team.</flux:text>
        </div>
        <flux:input label="Team Name" placeholder="Acme Inc" wire:model="name" />

        <div class="flex gap-2">
            <flux:spacer />
            <flux:modal.close>
                <flux:button variant="ghost" type="button">Cancel</flux:button>
            </flux:modal.close>
            <flux:button type="submit" variant="primary">Create</flux:button>
        </div>
    </form>
</flux:modal>
