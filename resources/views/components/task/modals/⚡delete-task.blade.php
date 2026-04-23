<?php

use Livewire\Component;
use Livewire\Attributes\Locked;
use Flux\Flux;

new class extends Component {
    #[Locked]
    public $task;

    public function delete_task()
    {
        if ($this->task->project->status !== 'active') {
            Flux::toast(variant: 'danger', heading: 'Project is not active!', text: 'Tasks can only be deleted in active projects.');
            return;
        }

        $this->task->delete();

        Flux::toast(variant: 'success', heading: 'Deleted task', text: 'Task #' . $this->task->id . ' deleted successfully.');
        Flux::modals()->close();

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
