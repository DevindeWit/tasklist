<?php

use Livewire\Component;
use App\Models\Task;
use Flux\Flux;
use Illuminate\Validation\ValidationException;

new class extends Component {
    public $task_title;

    public function create_task()
    {
        try {
            $this->validate([
                'task_title' => 'required|string|min:3|max:255',
            ]);

            Task::create([
                'title' => $this->task_title,
            ]);

            Flux::toast(variant: 'success', heading: 'Task created', text: 'Your task has been added successfully.');

            $this->reset('task_title');
        } catch (ValidationException $e) {
            Flux::toast(variant: 'danger', heading: 'Validation Error', text: $e->validator->errors()->first('task_title'));
        }
    }
};
?>

<flux:card class="p-2 flex flex-col gap-4">
    <div class="flex justify-between items-center">
        <flux:heading size="lg">Create new task</flux:heading>
        <flux:button icon="x-mark" class="cursor-pointer" variant="ghost" size="sm"
            wire:click="$dispatch('create-task')"></flux:button>
    </div>

    <flux:input placeholder="Task title" class="bg-zinc-800/80 rounded-lg" wire:model.change='task_title'
        wire:keydown.enter='create_task'>
        <x-slot name="iconTrailing">
            <flux:button size="sm" variant="subtle" icon="plus" icon:variant="outline" class="-mr-1"
                wire:click='create_task' />
        </x-slot>
    </flux:input>
</flux:card>
