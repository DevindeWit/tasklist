<?php

use Livewire\Component;

new class extends Component
{

};
?>

<div>
    @if (! auth()->user()?->team_id)
        <x-team.no-team-found />
    @endif
</div>
