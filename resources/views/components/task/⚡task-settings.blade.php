<?php

use Livewire\Component;
use Flux\Flux;
use Illuminate\Validation\ValidationException;

new class extends Component {
    public $task;

    public $new_data = [];

    public function save_changes()
    {
        $this->dispatch('create-task');

        try {
            $this->validate([
                'new_data.title' => 'required|string|min:3|max:255',
                'new_data.description' => 'nullable|string|max:1000',
            ]);
        } catch (ValidationException $e) {
            Flux::toast(variant: 'danger', heading: 'Validation Error', text: $e->validator->errors()->first('new_data.title'));
        }
    }

    public function mount(): void
    {
        $this->new_data = [
            'title' => $this->task->title ?? '',
            'description' => $this->task->description ?? '',
            'status' => $this->task->status ?? '',
            'priority' => $this->task->priority ?? '',
            'due_date' => $this->task->due_date ?? '',
            'estimate_minutes' => $this->task->estimate_minutes ?? '',
            'assignee_id' => $this->task->assignee_id ?? '',
        ];
    }
};
?>

<div class="flex flex-col gap-6">

    <flux:field>
        <flux:label badge="Required">Task title</flux:label>

        <flux:input :placeholder="$task->title" wire:model.debounce.500ms='new_data.title' autocomplete="off" wire:keydown.enter='save_changes' />
    </flux:field>

    <div class="flex justify-between">
        <flux:modal.trigger :name="'delete-task-' . ($task->id ?? 'new')">
            <flux:button variant="danger" icon="trash"></flux:button>
        </flux:modal.trigger>

        <div>
            <flux:modal.close>
                <flux:button variant="ghost">Close</flux:button>
            </flux:modal.close>

            <flux:button variant="primary" wire:click='save_changes'>Save changes</flux:button>
        </div>
    </div>

    @teleport('body')
        <flux:modal :name="'delete-task-' . ($task->id ?? 'new')">
            <livewire:task.delete-task :task_id="$task->id ?? 'new'" wire:key="delete-task-{{ $task->id ?? 'new' }}" />
        </flux:modal>
    @endteleport
</div>
