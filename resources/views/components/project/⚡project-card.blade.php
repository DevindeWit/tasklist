<?php

use Livewire\Component;

new class extends Component {
    // Project data received through parent
    public $project;
};
?>

<flux:card class="space-y-6 hover:bg-zinc-50 dark:hover:bg-zinc-700 cursor-pointer transition">
    <div>
        <div class="flex justify-between">
            <flux:heading size="lg">{{ $project->name }}</flux:heading>

            @if (auth()->user()->role !== 'member')
                <flux:modal.trigger :name="'project-settings-'.$project->id" @click.stop>
                    <flux:button variant="ghost" size="sm" icon="cog-6-tooth" icon:variant="outline"
                        inset="top right bottom" class="cursor-pointer" />
                </flux:modal.trigger>
            @endif
        </div>


        @if (strlen($project->description) > 0)
            <flux:tooltip>
                <flux:text class="mt-2 whitespace-pre-line line-clamp-2 leading-5 min-h-10">
                    {{ $project->description }}
                </flux:text>

                <flux:tooltip.content class="max-w-sm">
                    {{ $project->description }}
                </flux:tooltip.content>
            </flux:tooltip>
        @else
            <flux:text class="mt-2 whitespace-pre-line line-clamp-2 leading-5 min-h-10">
                {{ $project->description }}
            </flux:text>
        @endif

    </div>

    <div class="m-0 flex gap-4 *:opacity-70 *:hover:opacity-100 *:transition *:cursor-text">
        <flux:badge class="text-xs">
            {{ $project->code }}
        </flux:badge>

        <flux:badge class="text-xs">
            {{ $project->tasks->whereNotIn('status', ['done', 'blocked'])->count() }} active tasks
        </flux:badge>

        <flux:badge class="text-xs">
            {{ collect($project->tasks)->pluck('assignee_id')->unique()->count() }} assigned users
        </flux:badge>
    </div>

    @teleport('body')
        <flux:modal :name="'project-settings-'.$project->id">
            <livewire:project.project-settings :project="$project" wire:key="'project-settings-'.$project->id" />
        </flux:modal>
    @endteleport
</flux:card>
