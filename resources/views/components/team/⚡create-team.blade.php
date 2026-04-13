<?php

use Livewire\Component;
use App\Models\Team;
use Illuminate\Database\UniqueConstraintViolationException;

new class extends Component {
    public string $team_name = '';
    public string $error = '';

    public function createTeam()
    {
        $this->validate([
            'team_name' => 'required|string|max:255',
        ]);

        try {
            $team = Team::create([
                'name' => $this->team_name,
                'owner_id' => auth()->id(),
            ]);

            auth()->user()->update(['team_id' => $team->id]);

            $this->dispatch('close');
            $this->dispatch('teamUpdated');
        } catch (UniqueConstraintViolationException $e) {
            $this->error = 'A team with this name already exists. Please choose a different name.';
        }
    }
};
?>

<div class="space-y-6">
    <div>
        <flux:heading size="lg">Create team</flux:heading>
        <flux:text class="mt-2">Enter a name for your new team</flux:text>
    </div>

    <flux:input label="Name" placeholder="Team name" wire:model.live="team_name" />

    <div class="flex justify-between items-center gap-4">
        <flux:text>{{ $error }}</flux:text>
        <flux:button variant="primary" wire:click="createTeam">Create</flux:button>
    </div>
</div>

