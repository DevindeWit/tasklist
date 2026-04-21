<?php

use Livewire\Component;
use Flux\Flux;
use App\Models\Task;

new class extends Component {
    public Task $task;
    public $new_data = [];

    public function save_changes()
    {
        $this->dispatch('create-task');

        $this->validate([
            'new_data.title' => 'required|string|min:3|max:255',
            'new_data.description' => 'nullable|string|max:1000',
            'new_data.due_date' => 'nullable|date|after_or_equal:today',
        ]);

        $this->task->update($this->new_data);

        Flux::modals()->close();
        Flux::toast(variant: 'success', heading: 'Task created successfully!', text: $this->new_data['title']);
        $this->redirect(route('tasks.index', ['project_code' => $this->task->project->code]), navigate: true);

        unset($this->new_data);

        // Flux::toast(variant: 'danger', heading: 'Validation Error', text: $e->validator->errors()->first('new_data.title'));
    }

    public function mount(): void
    {
        $this->new_data = [
            'title' => $this->task->title ?? null,
            'description' => $this->task->description ?? null,
            'status' => $this->task->status ?? null,
            'priority' => $this->task->priority ?? null,
            'due_date' => $this->task->due_date ?? null,
            'estimate_minutes' => $this->task->estimate_minutes ?? null,
            'assignee_id' => $this->task->assignee_id ?? null,
        ];
    }
};
?>

<div class="flex flex-col gap-6 w-[80vw]! max-w-120">

    <flux:heading size="lg">Change task settings</flux:heading>

    {{-- Title --}}
    <flux:field>
        <flux:label badge="Required">Task title</flux:label>

        <flux:input :placeholder="$task->title ?? ''" wire:model.debounce.500ms='new_data.title' autocomplete="off"
            wire:keydown.enter='save_changes' />
    </flux:field>

    {{-- Description --}}
    <flux:field>
        <flux:label>Description</flux:label>
        <flux:textarea resize="none" :placeholder="$task->description ?? ''"
            wire:model.debounce.500ms='new_data.description' class=" " rows="10"></flux:textarea>
    </flux:field>

    {{-- Smaller fields (assignee, date, time, etc) --}}
    <div class="grid grid-cols-2 gap-4 items-start justify-items-stretch">

        {{-- Due date using native picker with Flux styling --}}
        <flux:field>
            <flux:label>Due date</flux:label>

            <flux:input type="date" wire:model="new_data.due_date" class="cursor-pointer appearance-none" />

            <flux:error name="new_data.due_date" />
        </flux:field>

        {{-- Assignee ("smaller" >.> ) --}}
        <flux:field>
            <flux:label>Assignee</flux:label>

            @php
                $teamUsers = auth()->user()->team->users;
                $assigneeId = $new_data['assignee_id'] ?? null;
                $selectedUser = $assigneeId ? $teamUsers->firstWhere('id', $assigneeId) : null;
            @endphp

            <flux:dropdown>
                <div class="grid w-full min-w-0 cursor-pointer">
                    <div class="min-w-0 overflow-hidden">
                        <flux:card wire:loading.remove class="!w-full !min-w-0 p-0 rounded-lg cursor-pointer">
                            <div class="-m-[1px]">
                                <flux:profile class="!w-full !min-w-0 truncate rounded-lg cursor-pointer"
                                    name="{{ $selectedUser?->name }}" />
                            </div>
                        </flux:card>
                    </div>

                    <flux:card wire:loading class="flex p-1.5 rounded-lg">
                        <flux:icon.loading />
                    </flux:card>
                </div>

                <flux:menu class="space-y-1.5" x-data="{
                    search: '',
                    isMatch(name) {
                        const term = this.search.toLowerCase().trim();
                        return term === '' || name.toLowerCase().includes(term);
                    }
                }">
                    <flux:input class="p-1" icon-trailing="magnifying-glass" x-model="search"
                        x-on:input="search = $event.target.value" @keydown.stop @click.stop
                        placeholder="Search team..." />

                    @if ($selectedUser)
                        <flux:profile name="{{ $selectedUser->name }}" class="w-full cursor-pointer"
                            icon:trailing="x-mark" wire:click="$set('new_data.assignee_id', null)" />
                        <flux:separator />
                    @endif

                    <div class="max-h-64 overflow-y-auto">
                        @foreach ($teamUsers->filter(fn($u) => $u->id !== $assigneeId) as $user)
                            <div wire:key="wrapper-user-{{ $user->id }}"
                                x-show="isMatch(@js($user->name))">
                                <flux:profile name="{{ $user->name }}" class="w-full cursor-pointer"
                                    :chevron="false"
                                    wire:click="$set('new_data.assignee_id', {{ $user->id }})" />
                            </div>
                        @endforeach
                    </div>
                </flux:menu>
            </flux:dropdown>
        </flux:field>
    </div>


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
