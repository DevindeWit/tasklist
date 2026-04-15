<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div class="min-h-full flex flex-col gap-4">
    @if (auth()->user()->team->projects->isEmpty())
        <div class="h-full">
            <livewire:project.no-projects-found />
        </div>
    @else
        <flux:heading size="xl">Projects:</flux:heading>

        <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 xl:grid-cols-3">
            @foreach (auth()->user()->team->projects as $project)
                <livewire:project.project-card :project="$project" wire:key="project-{{ $project->id }}" />
            @endforeach

            @if (auth()->user()->role !== 'member')
                <flux:modal.trigger name="create_project">
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

    @teleport('body')
        <flux:modal name="create_project" :closeable="true">
            <livewire:project.create-project />
        </flux:modal>
    @endteleport
</div>
