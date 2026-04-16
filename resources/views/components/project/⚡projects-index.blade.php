<?php

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\Project;

new class extends Component {
    // Project ID retrieved through URL parameter
    #[Url]
    public $project_id;

    public function open_project($projectId)
    {
        $this->redirect(route('projects', ['project_id' => $projectId]), navigate: true);
    }

    public function mount()
{
    if (!$this->project_id) {
        return;
    }

    $project = Project::findOrFail($this->project_id); // 404 if not found

    abort_if(
        $project->team_id !== auth()->user()->team_id,
        403
    );
}
};
?>

<div class="min-h-full flex flex-col gap-4">
    @if (is_numeric($project_id) && strlen($project_id) > 0)
        <livewire:project.project-show :project_id="$project_id" />
    @else
        @if (auth()->user()->team->projects->isEmpty())
            <div class="h-full">
                <livewire:project.no-projects-found class="h-full" />
            </div>
        @else
            <flux:heading size="xl">Projects:</flux:heading>

            <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 xl:grid-cols-3">
                @foreach (auth()->user()->team->projects as $project)
                    <div wire:click='open_project({{ $project->id }})'>
                        <livewire:project.project-card :project="$project" wire:key="project-{{ $project->id }}" />
                    </div>
                @endforeach

                @if (auth()->user()->role !== 'member')
                    <flux:modal.trigger name="create-project">
                        <flux:card
                            class="flex flex-col items-center justify-center border-dashed border-2 text-zinc-50 hover:bg-zinc-50 dark:hover:bg-zinc-700 cursor-pointer opacity-70 hover:opacity-100 transition">
                            <flux:icon name="plus" />
                            <flux:text class="mt-2">New project</flux:text>
                        </flux:card>
                    </flux:modal.trigger>
                @else
                    <flux:card
                        class="flex flex-col items-center justify-center border-dashed border-2 text-zinc-50 hover:bg-zinc-50 dark:hover:bg-zinc-700 opacity-70">
                        <flux:icon name="user" />
                        <flux:text class="mt-2">Want to add a new project?<br>Contact your team manager.</flux:text>
                    </flux:card>
                @endif
            </div>
        @endif
    @endif

    @teleport('body')
        <flux:modal name="create-project">
            <livewire:project.create-project />
        </flux:modal>
    @endteleport
</div>
