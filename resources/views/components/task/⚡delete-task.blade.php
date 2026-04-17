<?php

use Livewire\Component;
use Livewire\Attributes\Locked;
use App\Models\Task;
use Flux\Flux;

new class extends Component {
    #[Locked]
    public $task_id;

    public function delete_task()
    {
        $this->validate([
            'task_id' => 'required|exists:tasks,id|numeric',
        ]);

        Task::findOrFail($this->task_id)->delete();

        Flux::toast(variant: 'success', heading: 'Deleted task', text: 'Task #' . $this->task_id . ' deleted successfully.');

        $this->dispatch('refresh-kanban');
    }
};
?>

<div class="space-y-6">
    <div>
        <flux:heading size="lg">Are you sure?</flux:heading>
        <flux:text class="mt-2">Deleting a task can not be undone.</flux:text>
    </div>

    <div class="flex justify-between">
        <flux:modal.close>
            <flux:button variant="ghost">Cancel</flux:button>
        </flux:modal.close>

        <flux:button variant="danger" wire:click='delete_task'>Delete</flux:button>
    </div>
</div>
