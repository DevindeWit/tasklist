<?php

use Livewire\Component;
use App\Models\Team;
use Flux\Flux;

new class extends Component {
    public string $team_name = '';

    public function createTeam()
    {
        try {
            $this->validate([
                'team_name' => 'required|string|max:255|unique:teams,name',
            ]);

            $team = Team::create([
                'name' => $this->team_name,
                'owner_id' => auth()->id(),
            ]);

            auth()->user()->update(['team_id' => $team->id]);

            Flux::toast(
                variant: 'success',
                heading: 'Created Team',
                text: 'Welcome to ' . $team->name . '!'
            );

            $this->redirect(route('team'), navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Flux::toast(
                variant: 'danger',
                heading: 'Validation Error',
                text: $e->validator->errors()->first('team_name')
            );
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
        <flux:button variant="primary" wire:click="createTeam">Create</flux:button>
    </div>
</div>
