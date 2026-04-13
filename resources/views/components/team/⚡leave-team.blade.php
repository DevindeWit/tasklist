<?php

use Livewire\Component;

new class extends Component {
    public function leaveTeam()
    {
        auth()->user()->update(['team_id' => null]);

        $this->redirect(route('team'));
    }
};

?>

<div>
    <flux:heading>Leave Team</flux:heading>
    <flux:text>Are you sure you want to leave this team? This action cannot be undone.</flux:text>

    <div class="flex gap-3 mt-6">
        <flux:button variant="outline" @click="$dispatch('close')">Cancel</flux:button>
        <flux:button variant="danger" wire:click="leaveTeam">Leave Team</flux:button>
    </div>
</div>
