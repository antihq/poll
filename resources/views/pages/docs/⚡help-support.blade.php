<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::doc'), Title('Help & Support')] class extends Component
{
    //
};
?>

<div>
    <flux:text class="mb-2 mt-0! font-medium">Support</flux:text>

    <h1>Help & Support</h1>

    <h2>FAQ</h2>

    <p><strong>Q: Can I change the order of answers after creating a poll?</strong></p>

    <p>
        <strong>A:</strong>
        Yes! You can drag and drop answers to reorder them in both the create and edit screens.
    </p>

    <hr class="my-12" />

    <p><strong>Q: What happens if I delete a poll?</strong></p>

    <p>
        <strong>A:</strong>
        Deleting a poll permanently removes all associated answers and responses. This action cannot be undone.
    </p>

    <hr class="my-12" />

    <p><strong>Q: Can I export poll responses?</strong></p>

    <p>
        <strong>A:</strong>
        Currently, you can view response details in the interface. Export functionality may be added in future updates.
    </p>

    <hr class="my-12" />

    <p><strong>Q: How many polls can I create?</strong></p>

    <p>
        <strong>A:</strong>
        Unlimited! Antipoll includes unlimited poll creation with your subscription - no limits or restrictions.
    </p>

    <hr class="my-12" />

    <p><strong>Q: Can I customize the poll appearance?</strong></p>

    <p>
        <strong>A:</strong>
        Yes! You can choose between vertical and horizontal layouts, and hide Antipoll branding for a white-label
        experience. All customization features are included with your subscription.
    </p>

    <h2>Contact Us</h2>

    <p>For additional support or questions, please contact the Antipoll team.</p>
</div>
