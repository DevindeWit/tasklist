@props(['task'])

<div class="flex *:data-button:cursor-pointer gap-2">
    <flux:modal.trigger name="delete-task-{{ $task->id }}">
        <flux:button variant="danger" icon="trash" icon:variant="outline" data-button />
    </flux:modal.trigger>


    <flux:spacer />

    <flux:button variant="ghost">Reset</flux:button>
    <flux:button variant="primary">Save</flux:button>

    @teleport('body')
        <flux:modal name="delete-task-{{ $task->id }}">
            <livewire:task.modals.delete-task :task="$task" />
        </flux:modal>
    @endteleport
</div>
