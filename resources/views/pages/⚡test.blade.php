<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::simple'), Title('Login')] class extends Component
{
    //
};
?>

<div>
    <livewire:one-time-password />
</div>
