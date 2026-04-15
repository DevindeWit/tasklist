<?php

use Livewire\Component;
use Flux\Flux;
use Illuminate\Support\Str;

new class extends Component {
    public function deleteTeam()
    {
        $team = auth()->user()->team;

        Flux::toast(variant: 'success', heading: 'Team Deleted', text: $team->name . ' has been deleted successfully.');

        // Update all users in this team to have no team (except the owner)
        $team
            ->users()
            ->where('id', '!=', $team->owner_id)
            ->update(['team_id' => null, 'acknowledge' => 'deleted']);

        // Change name of team to indicate it's deleted (and to free up the original name for future teams)
        $team->update([
            'name' => $team->name . '-deleted-' . now()->timestamp,
        ]);

        // Delete the team
        $team->delete();

        $this->redirect(route('team'), navigate: true);
    }
};
?>

<div class="space-y-6" x-data="{ confirmText: '', teamName: '{{ auth()->user()->team->name }}' }">
    <div>
        <flux:heading size="lg">Delete Team</flux:heading>
        <flux:text class="mt-2">
            Please write

            <cell class="bg-[#fff2]! px-1 mx-1 border-solid border border-[#fff3] rounded">
                sudo delete
                {{ auth()->user()->team->name }}
            </cell>

            to confirm the deletion.
        </flux:text>
    </div>

    <flux:text>A total of<u> {{ auth()->user()->team->users()->count() }} </u>user(s) will be affected.</flux:text>

    <flux:input label="Confirm" x-model="confirmText"></flux:input>

    <div class="flex gap-3 mt-6 justify-between">
        <flux:modal.close>
            <flux:button variant="ghost">Cancel</flux:button>
        </flux:modal.close>

        <flux:button variant="danger" wire:click="deleteTeam"
            x-bind:disabled="confirmText !== 'sudo delete ' + teamName">Delete Team</flux:button>
    </div>
</div>
