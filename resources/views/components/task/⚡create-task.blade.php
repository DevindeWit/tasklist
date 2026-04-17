<?php

use Livewire\Component;
use App\Models\Project;
use Flux\Flux;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Url;

new class extends Component {
    // Retrieved from parent
    public $status;

    public $task_title;

    // Project ID retrieved through URL parameter
    #[Url]
    public $project_id;

    public function create_task()
    {
        try {
            $project = Project::where('id', $this->project_id)
                ->where('team_id', auth()->user()->team_id)
                ->firstOrFail();

            $this->validate([
                'task_title' => 'required|string|min:3|max:255',
                'project_id' => 'required|integer|exists:projects,id',
            ]);

            $project->tasks()->create([
                'title' => $this->task_title,
                'status' => $this->status,
            ]);

            $this->dispatch('refresh-kanban');
            Flux::modal('task-settings-new')->show();

            $this->reset(['task_title']);
        } catch (ValidationException $e) {
            Flux::toast(variant: 'danger', heading: 'Validation Error', text: $e->validator->errors()->first('task_title'));
        }
    }
};
?>

<flux:card wire:key="create-task-card" class="p-2 flex flex-col gap-4">
    <div class="flex justify-between items-center">
        <flux:heading size="lg">Create new task</flux:heading>
        <flux:button icon="x-mark" variant="ghost" size="sm" wire:click="$dispatch('create-task')" />
    </div>

    {{--
        The wrapper ensures x-init isn't shadowed by Flux's internal Alpine logic.
        We target the native input element specifically.
        The 100ms delay is a safety margin for components rendered via Livewire toggles.
    --}}
    <div x-data x-init="setTimeout(() => $el.querySelector('input')?.focus(), 100)">
        <flux:input placeholder="Task title" class="bg-zinc-800/80 rounded-lg" wire:model.change="task_title"
            wire:keydown.enter="create_task" autocomplete="off">
            <x-slot name="iconTrailing">
                <flux:button size="sm" variant="subtle" icon="plus" icon:variant="outline" class="-mr-1"
                    wire:click="create_task" />
            </x-slot>
        </flux:input>
    </div>
</flux:card>
