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

<div class="flex flex-col gap-6">

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
            wire:model.debounce.500ms='new_data.description' class=" w-[80vw]! max-w-120" rows="10"></flux:textarea>
    </flux:field>

    {{-- Assignee --}}
    <flux:field>
        <flux:label>Assignee</flux:label>

        <flux:dropdown>
            @if (!empty($new_data['assignee_id']))
                <flux:card class="w-fit p-0 rounded-lg">
                    <flux:profile wire:loading.remove class="rounded-lg cursor-pointer"
                        name="{{ auth()->user()->team->users->firstWhere('id', $new_data['assignee_id'])->name }}">
                    </flux:profile>
                </flux:card>
            @else
                <flux:button icon:trailing="chevron-down" wire:loading.remove>Select assignee</flux:button>
            @endif

            <flux:card wire:loading class="p-1.5 flex rounded-lg">
                <flux:icon.loading />
            </flux:card>

            <flux:menu class="space-y-1.5" x-data="{
                search: '',
                /* Convert team users to a JS-accessible array for logic checks */
                users: {{ auth()->user()->team->users->map(fn($u) => ['id' => $u->id, 'name' => strtolower($u->name)])->toJson() }},
                /* Check if the current search string matches any user in the list */
                get hasMatches() {
                    if (!this.search) return true;
                    return this.users.some(u => u.name.includes(this.search.toLowerCase()));
                }
            }">
                <flux:input class="p-1" icon-trailing="magnifying-glass" x-model="search" {{-- Stop propagation to prevent the menu from hijacking keystrokes for navigation --}}
                    @keydown.stop {{-- Prevent the menu from closing when the input is clicked --}} @click.stop />

                <flux:separator />

                <div class="max-h-64 overflow-y-auto">
                    @foreach (auth()->user()->team->users as $user)
                        <flux:profile name="{{ $user->name }}" class="w-full cursor-pointer" :chevron="false"
                            {{-- Logic: Show if search is empty, or name matches, or if NO matches exist globally (fallback) --}}
                            x-show="! search || '{{ strtolower($user->name) }}'.includes(search.toLowerCase()) || ! hasMatches"
                            wire:click="$set('new_data.assignee_id', {{ $user->id }})" />
                    @endforeach
                </div>
            </flux:menu>
        </flux:dropdown>
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
