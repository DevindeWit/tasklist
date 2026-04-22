<?php

use Livewire\Component;
use App\Models\Project;

new class extends Component {
    // Project data received through parent
    public Project $project;

    public function delete_project()
    {
        $this->project->delete();

        $this->redirect(route('projects'), navigate: true);

        Flux::toast(variant: 'success', heading: 'Project deleted', text: "Your project \"{$this->project->name}\" has been permanently deleted.");
    }
};
?>

<div class="space-y-6">
    <div>
        <flux:heading size="lg">Are you sure?</flux:heading>
        <flux:text class="mt-2">This will PERMANENTLY delete this project.</flux:text>
    </div>

    <div class="flex justify-between">
        <flux:modal.close>
            <flux:button variant="ghost" class="cursor-pointer">Cancel</flux:button>
        </flux:modal.close>

        <flux:button variant="danger" class="cursor-pointer" wire:click='delete_project'>Delete Project</flux:button>
    </div>
</div>
