<?php

use Livewire\Component;

new class extends Component {
    // retrieved from parent
    public $tasks;

    public array $statuses = ['todo' => 'document', 'doing' => 'document-text', 'blocked' => 'archive-box', 'done' => 'document-check'];
};
?>

<div class="flex flex-nowrap gap-4 overflow-x-auto overflow-y-hidden pb-4 *:w-md *:shrink-0">
    @foreach ($this->statuses as $status => $icon)
        <flux:card wire:key="card-{{ $status }}" class="p-4 flex flex-col gap-4 overflow-y-hidden">

            <div class="flex justify-between items-center">
                <div class="flex gap-2 items-center">
                    <flux:icon name="{{ $icon }}" class="size-5"></flux:icon>
                    <flux:heading size="lg">{{ ucfirst($status) }}</flux:heading>
                </div>


                @if (auth()->user()->role !== 'member')
                    <flux:button icon="plus" size="sm" variant="ghost" class="cursor-pointer" tooltip="Add new task"></flux:button>
                @endif
            </div>

            <div class="flex flex-col gap-4 overflow-y-auto pr-2 custom-scrollbar">
                @foreach ($tasks->where('status', $status) as $task)
                    <livewire:project.project-kanban-task :task="$task" wire:key="'task-'.$task->id" />
                @endforeach

                @if (auth()->user()->role !== 'member')
                    <flux:card
                        class="flex flex-col items-center justify-center border-dashed border-2 text-zinc-50 hover:bg-zinc-50 dark:hover:bg-zinc-700 cursor-pointer opacity-70 hover:opacity-100 transition">
                        <flux:icon name="plus" />
                        <flux:text class="mt-2">New task</flux:text>
                    </flux:card>
                @endif
            </div>

        </flux:card>
    @endforeach
</div>
