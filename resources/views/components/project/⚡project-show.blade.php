<?php

use Livewire\Component;
use App\Models\Project;

new class extends Component {
    public $project_id;

    public $project;
    public $tasks;

    public function mount()
    {
        $this->project = Project::find($this->project_id);
        $this->tasks = $this->project->tasks;
    }
};
?>

<div class="flex flex-col gap-8 h-[calc(100dvh-4rem)]">
    <div>
        <div class="flex justify-between">
            <flux:heading size="xl">{{ $project->name }}</flux:heading>

            @if (auth()->user()->role !== 'member')
                <flux:modal.trigger :name="'project-settings-'.$project->id">
                    <flux:button icon="cog-6-tooth"></flux:button>
                </flux:modal.trigger>
            @endif
        </div>

        <flux:subheading>{!! strlen($project->description) > 0 ? $project->description : '&nbsp;' !!}</flux:subheading>
    </div>

    <livewire:project.project-kanban :tasks="$tasks" wire:key="'project-kanban-'.$project->id" class="flex-1" />

    @teleport('body')
        <flux:modal :name="'project-settings-'.$project->id">
            <livewire:project.project-settings :project="$project" wire:key="'project-settings-'.$project->id" />
        </flux:modal>
    @endteleport
</div>
