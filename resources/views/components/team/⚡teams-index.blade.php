<?php

use Livewire\Component;

new class extends Component
{

};
?>

<div>
    @if (! auth()->user()?->team_id)
        <x-team.no-team-found />
    @else
        <flux:modal.trigger name="leave-team">
            <flux:button variant="danger" @click="$dispatch('openModal')">Leave Team</flux:button>
        </flux:modal.trigger>
    @endif

    @teleport('body')
        <flux:modal name="leave-team">
            <livewire:team.leave-team />
        </flux:modal>
    @endteleport
</div>
