<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::doc'), Title('Viewing Responses')] class extends Component
{
    //
};
?>

<div>
    <flux:text class="mb-2 mt-0! font-medium">Documentation</flux:text>

    <h1>Viewing Responses</h1>

    <h2>Response Analytics</h2>

    <p>Track your poll performance with comprehensive analytics:</p>

    <ul>
        <li>
            <strong>Response counts</strong>
            - Total and per-answer statistics
        </li>
        <li>
            <strong>Percentage breakdowns</strong>
            - Visual representation of answer distribution
        </li>
        <li>
            <strong>Response details</strong>
            - Individual response data with timestamps
        </li>
    </ul>

    <h2>Answer Details</h2>

    <p>Drill down into specific answers to see:</p>

    <ul>
        <li>
            <strong>Respondent emails</strong>
            - When email collection is enabled
        </li>
        <li>
            <strong>Feedback responses</strong>
            - Qualitative feedback from respondents
        </li>
        <li>
            <strong>Response timestamps</strong>
            - When each response was submitted
        </li>
    </ul>

    <img src="/assets/images/CleanShot 2025-12-08 at 11.13.40@2x.png" />
</div>
