<?php

use Livewire\Component;
use App\Models\Project;
use Flux\Flux;

new class extends Component {
    // Project data received through parent
    public Project $project;

    public bool $on_hold;

    // Values converted to array for model.live binding in inputs
    public array $project_array = [];

    /**
     * Update project details and redirect to the task view.
     */
    public function save_changes()
    {
        $this->validate([
            'project_array.name' => 'required|string|min:3|max:255',
            'project_array.description' => 'nullable|string|max:1000',
        ]);

        $status = $this->project->status === 'archived' ? 'archived' : 'active';

        if ($this->on_hold) {
            $status = $this->project->status === 'archived' ? 'archived' : 'on_hold';
        }

        $this->project->update([
            'name' => $this->project_array['name'],
            'description' => $this->project_array['description'],
            'status' => $status,
        ]);

        /**
         * The route helper must use 'project_code' to match:
         * Route::livewire('/tasks/{project_code?}', ...)
         */
        if (request()->route('project_code')) {
            $this->redirect(route('tasks.index', ['project_code' => $this->project->code]), navigate: true);
        } else {
            $this->redirect(route('projects'), navigate: true);
        }
    }

    public function recover_project()
    {
        $this->project->update(['status' => 'active']);

        $this->redirect(route('projects'), navigate: true);

        Flux::toast(variant: 'success', heading: 'Project recovered', text: "Your project \"{$this->project->name}\" has been recovered successfully.");
    }

    /**
     * Initialize the form array from the project instance.
     */
    public function mount()
    {
        $this->project_array = $this->project->toArray();
        $this->on_hold = $this->project->status === 'on_hold';
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
        <flux:label>Project name</flux:label>
        <flux:tooltip content="Name can't be edited while archived." :disabled="$project->status !== 'archived'">
            <flux:input :disabled="$project->status === 'archived'" placeholder="My cool website"
                wire:model="project_array.name" autocomplete="off" />
        </flux:tooltip>
        <flux:error name="project_array.name"></flux:error>
    </flux:field>

    {{-- Project description --}}
    <flux:field>
        <flux:label>Project description</flux:label>
        <flux:tooltip content="Description can't be edited while archived." :disabled="$project->status !== 'archived'">
            <flux:textarea :disabled="$project->status === 'archived'" placeholder="Describe your project..."
                wire:model="project_array.description" resize="none" />
        </flux:tooltip>
        <flux:error name="project_array.description"></flux:error>
    </flux:field>

    {{-- On hold ? --}}
    <flux:tooltip class="w-fit">
        <flux:field variant="inline" class="w-fit mb-6" :disabled="$project->status === 'archived' ? true : false">
            <flux:label>On hold</flux:label>
            <flux:switch wire:model="on_hold" :class="$project->status === 'archived' ? null : 'cursor-pointer'" />
            <flux:error name="on_hold" />
        </flux:field>

        @if ($project->status === 'archived')
            <flux:tooltip.content>
                Status can't be edited while archived.
            </flux:tooltip.content>
        @endif

    </flux:tooltip>

    {{-- Buttons --}}
    @if ($project->status !== 'archived')
        {{-- Not archived yet --}}
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
    @else
        {{-- Already in archive --}}
        <div class="flex justify-between">
            <flux:modal.trigger :name="'confirm-delete-project-'.$project->id">
                <flux:button icon="trash" variant="danger" class="cursor-pointer" />
            </flux:modal.trigger>

            <flux:button variant="primary" class="cursor-pointer" wire:click='recover_project'>Recover</flux:button>
        </div>
    @endif

    @teleport('body')
        <div>
            <flux:modal :name="'delete-project-'.$project->id">
                <livewire:project.delete-project :project="$project" wire:key="delete-project-{{ $project->id }}" />
            </flux:modal>

            <flux:modal :name="'confirm-delete-project-'.$project->id">
                <livewire:project.confirm-delete-project :project="$project"
                    wire:key="confirm-delete-project-{{ $project->id }}" />
            </flux:modal>
        </div>
    @endteleport
</div>
