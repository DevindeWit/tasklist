<?php

use Livewire\Component;
use App\Models\Project;
use Livewire\Attributes\Title;

new #[Title('Projects')] class extends Component {
    public function open_project($project_code)
    {
        $this->redirect(route('tasks.index', ['project_code' => $project_code]), navigate: true);
    }
};
?>

<div class="min-h-full flex flex-col gap-4">

    @if (auth()->user()->team->projects->isEmpty())
        <div class="h-full">
            <livewire:project.no-projects-found class="h-full" />
        </div>
    @else
        <flux:heading size="xl">Projects:</flux:heading>

        <div
            class="flex flex-col gap-8 *:flex *:flex-col *:gap-2 *:p-2 md:*:p-4 *:-mx-4 md:*:m-0 *:rounded-xl *:bg-zinc-900/40">

            {{-- Active projects --}}
            <div>
                <flux:heading size="lg">Active projects</flux:heading>

                <div class="grid gap-2 md:gap-4 grid-cols-1 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach (auth()->user()->team->projects->where('status', '==', 'active') as $project)
                        {{-- Wrap in a container or use wire:navigate on a link for better UX --}}
                        <div wire:click="open_project('{{ $project->code }}')" class="cursor-pointer">
                            <livewire:project.project-card :project="$project" wire:key="project-{{ $project->code }}" />
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
                            <flux:text class="mt-2">Want to add a new project?<br>Contact your team manager.
                            </flux:text>
                        </flux:card>
                    @endif
                </div>
            </div>


            {{-- On hold projects --}}
            <div>
                <flux:heading size="lg">Projects on hold</flux:heading>

                <div class="grid gap-2 md:gap-4 grid-cols-1 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach (auth()->user()->team->projects->where('status', '==', 'on_hold') as $project)
                        <livewire:project.project-card :project="$project" wire:key="project-{{ $project->code }}" />
                    @endforeach
                </div>
            </div>

            {{-- Archived (trashed) --}}
            @if (auth()->user()->role !== 'member')
                <div class="!bg-transparent !p-0">
                    <flux:modal.trigger name="project-trashcan" class="">
                        <flux:card
                            class="w-fit flex flex-col items-center justify-center border-dashed border-2 text-zinc-50 hover:bg-zinc-50 dark:hover:bg-zinc-700 cursor-pointer opacity-70 hover:opacity-100 transition">
                            <flux:icon name="trash" />
                            @php
                                $archivedCount = auth()->user()->team->projects->where('status', 'archived')->count();
                            @endphp

                            <flux:text class="mt-2">{{ $archivedCount ? "$archivedCount items" : 'Empty' }}
                            </flux:text>
                        </flux:card>
                    </flux:modal.trigger>
                </div>
            @endif
        </div>
    @endif

    @teleport('body')
        <div>
            <flux:modal name="create-project">
                <livewire:project.create-project />
            </flux:modal>

            <flux:modal name="project-trashcan">
                <livewire:project.project-trashcan />
            </flux:modal>
        </div>
    @endteleport
</div>
