<?php

use Livewire\Component;
use App\Models\Project;
use Flux\Flux;

new class extends Component {
    // prop from parent
    public Project $project;

    public function mount()
    {
        if ($this->project->status !== 'active') {
            Flux::modal('project-inactive-' . $this->project->id)->show();
        }
    }

    public function acknowledge_permissions()
    {
        $this->redirect(route('projects'), navigate: true);
    }
};
?>

<div class="flex flex-col gap-8 !h-[calc(100vh-6.5rem)] md:h-[calc(100vh-4rem)]">
    <div>
        <div class="flex gap-2 items-center">
            <flux:heading size="xl">{{ $project->name }}</flux:heading>

            @if (auth()->user()->role !== 'member')
                <flux:modal.trigger :name="'project-settings-'.$project->id">
                    <flux:button icon="cog-6-tooth" icon:variant="outline" variant="ghost"></flux:button>
                </flux:modal.trigger>
            @endif
        </div>

        <flux:subheading>{!! strlen($project->description) > 0 ? $project->description : '&nbsp;' !!}</flux:subheading>
    </div>

    <livewire:task.kanban.board :project="$project" wire:key="'project-kanban-'.$project->id" class="flex-1" />

    @teleport('body')
        <div>
            <flux:modal :name="'project-settings-'.$project->id">
                <livewire:project.project-settings :project="$project" wire:key="'project-settings-'.$project->id" />
            </flux:modal>

            <flux:modal :name="'project-inactive-'.$project->id" :dismissible="false" @close="acknowledge_permissions">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">Heads up!</flux:heading>
                        <flux:text class="mt-2">This project is marked as "{{ str_replace('_', ' ', $project->status) }}".
                        </flux:text>
                    </div>

                    <div class="flex items-center gap-2">
                        <flux:text>Continuing to work on this project is prohibited.</flux:text>
                        <flux:badge>Error 403</flux:badge>
                    </div>


                    <flux:button wire:click='acknowledge_permissions' class="cursor-pointer">Acknowledge</flux:button>
                </div>
            </flux:modal>
        </div>
    @endteleport
</div>
