<?php

use Livewire\Component;
use App\Models\Project;
use Flux\Flux;

new class extends Component {
    public string $project_name = '';
    public string $project_description = '';
    public string $project_code = '';

    public function create_project()
    {
        try {
            $this->validate([
                'project_name' => 'required|string|max:255',
                'project_description' => 'nullable|string|max:1000',
            ]);

            $this->project_code = auth()->user()->team_id . '-' . strtoupper(fake()->bothify('???-###'));

            $project = Project::create([
                'name' => $this->project_name,
                'description' => empty($this->project_description) ? null : $this->project_description,
                'code' => $this->project_code,
                'team_id' => auth()->user()->team_id,
            ]);

            Flux::toast(variant: 'success', heading: 'Created project', text: 'Welcome to ' . $project->name . '!');

            $this->redirect(route('projects'), navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Flux::toast(variant: 'danger', heading: 'Validation Error', text: $e->validator->errors()->first('project_name'));
        }
    }
};
?>

<div class="space-y-6 w-sm">
    <div>
        <flux:heading size="lg">Create project</flux:heading>
        <flux:text class="mt-2">Enter a name for your project</flux:text>
    </div>

    <flux:field>
        <flux:label badge="Required">Project Name</flux:label>
        <flux:input placeholder="My cool website" wire:model.live="project_name" autocomplete="off" />
    </flux:field>

    <flux:field>
        <flux:label badge="optional">Description</flux:label>
        <flux:textarea placeholder="Describe your project" wire:model.live="project_description" resize="none"/>
    </flux:field>

    <div class="flex justify-between items-center gap-4">
        <flux:modal.close>
            <flux:button variant="ghost">Cancel</flux:button>
        </flux:modal.close>
        <flux:button variant="primary" wire:click="create_project">Create</flux:button>
    </div>
</div>
