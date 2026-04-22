<?php

use Livewire\Component;

new class extends Component {
    public $tasks;

    public function mount()
    {
        $this->tasks = auth()->user()->assignedTasks()->orderByDesc('updated_at')->limit(5)->get();
    }
};
?>

<div class="flex flex-col gap-4">
    <flux:heading size="xl">Recent tasks:</flux:heading>
    <div class="border-solid border border-zinc-600 p-2 w-full min-h-100 rounded-xl flex justify-center items-center">
        @if ($tasks->isEmpty())
            <div class="flex flex-col gap-2 items-center text-center">
                <flux:icon.user />
                <flux:text>Looks like you haven't been assigned to any tasks yet!</flux:text>
                <flux:text size="sm">Ask your team manager to assign some tasks to you</flux:text>
            </div>
        @else
            <div class="w-full flex gap-2 *:max-w-100">
                @foreach ($tasks as $task)
                    <livewire:task.kanban.task :task="$task" wire:key="task-{{ $task->id }}" />
                @endforeach
            </div>
        @endif
    </div>
</div>
