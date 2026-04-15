<?php

use Livewire\Component;

new class extends Component {
    // Project data received through parent
    public $project;

    // Values converted to array for model.live binding in inputs
    public array $project_array = [];

    public function save_changes()
    {
        $this->validate([
            'project_array.name' => 'required|string|max:255',
            'project_array.description' => 'nullable|string|max:1000',
        ]);

        $this->project->update([
            'name' => $this->project_array['name'],
            'description' => $this->project_array['description'],
        ]);

        $this->redirect(route('projects'), navigate: true);
    }

    public function mount()
    {
        $this->project_array = $this->project->toArray();
    }
};
?>

<div class="space-y-6">
    <div>
        <flux:heading size="lg">Project settings</flux:heading>
        <flux:text class="mt-2">Manage your project's configuration and preferences.</flux:text>
    </div>

    {{-- Project name --}}
    <flux:field>
        <flux:label>Project Name</flux:label>
        <flux:input placeholder="My cool website" wire:model.live="project_array.name" />
    </flux:field>

    {{-- Project description --}}
    <flux:field>
        <flux:label>Project Description</flux:label>
        <flux:textarea placeholder="Describe your project..." wire:model.live="project_array.description" resize="none" />
    </flux:field>

    <div class="flex gap-4">
        <flux:modal.trigger :name="'delete-project-'.$project->id">
            <flux:button icon="trash" variant="danger" class="cursor-pointer" />
        </flux:modal.trigger>

        <flux:spacer />

        <flux:modal.close>
            <flux:button variant="ghost" class="cursor-pointer">Cancel</flux:button>
        </flux:modal.close>

        <flux:button variant="primary" class="cursor-pointer" wire:click='save_changes'>Save changes</flux:button>
    </div>

    @teleport('body')
        <flux:modal :name="'delete-project-'.$project->id">
            <livewire:project.delete-project :project="$project" wire:key="'delete-project-'.$project->id" />
        </flux:modal>
    @endteleport
</div>
