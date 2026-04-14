<?php

use Livewire\Component;
use Flux\Flux;

new class extends Component {
    public function leaveTeam()
    {
        $team = auth()->user()->team;

        auth()
            ->user()
            ->update(['team_id' => null]);

        Flux::toast(
            variant: 'success',
            heading: 'Left Team',
            text: "You have left " . $team->name . "."
        );

        $this->redirect(route('team'), navigate: true);
    }
};
?>

<div class="space-y-6">
    <div>
        <flux:heading size="lg">Leave Team</flux:heading>
        <flux:text class="mt-2">Are you sure you want to leave this team?</flux:text>
    </div>

    <div class="flex gap-3 mt-6 justify-between">
        <flux:button variant="outline" @click="$dispatch('close')">Cancel</flux:button>
        <flux:button variant="danger" wire:click="leaveTeam">Leave Team</flux:button>
    </div>
</div>
