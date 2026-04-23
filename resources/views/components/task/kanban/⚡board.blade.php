<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Project;

new class extends Component {
    // retrieved from parent
    public Project $project;

    public $tasks;

    public array $statuses = [
        'todo' => [
            'icon' => 'document',
            'creating_new' => false,
        ],
        'doing' => [
            'icon' => 'document-text',
            'creating_new' => false,
        ],
        'blocked' => [
            'icon' => 'archive-box',
            'creating_new' => false,
        ],
        'done' => [
            'icon' => 'document-check',
            'creating_new' => false,
        ],
    ];

    #[On('create-task')]
    public function create_task(string $target = '', string $board = '')
    {
        // All to false
        foreach ($this->statuses as $status => $values) {
            $this->statuses[$status]['creating_new'] = false;
        }

        // If specified, target status to true
        if (array_key_exists($target, $this->statuses)) {
            $this->statuses[$target]['creating_new'] = true;

            $this->dispatch('scroll-to-bottom', column: $target, board: $board);
        }
    }

    #[On('refresh-kanban')]
    public function refresh_kanban()
    {
        $this->project = Project::findOrFail($this->project->id);
        $this->tasks = $this->project->tasks;
    }

    public function mount()
    {
        $this->tasks = $this->project->tasks;
    }
};
?>

<div x-data
    @scroll-to-bottom.window="
        $nextTick(() => {
            const card = document.getElementById(`kanban-${$event.detail.column}-${$event.detail.board}`);

            /**
             * scrollIntoView handles the alignment for all scrollable parents automatically.
             * behavior: smooth provides the animated transition.
             * block: end ensures the column scrolls to the bottom where the card resides.
             * inline: center performs the horizontal centering math previously done manually.
             */
            card?.scrollIntoView({
                behavior: 'smooth',
                block: 'end',
                inline: 'center'
            });
        })
    "
    class="flex flex-nowrap gap-4 overflow-x-auto overflow-y-hidden pb-4 px-4 *:w-md *:shrink-0 w-[calc(100%+4rem)] -translate-x-8 translate-y-8 -mt-8 custom-scrollbar scroll-smooth">
    @foreach ($this->statuses as $type => $status)
        <flux:card wire:key="card-{{ $type }}" class="p-4 flex flex-col gap-4 overflow-y-hidden max-w-[80vw]"
            id="kanban-{{ $type }}-{{ $tasks->first()->project->id ?? '1' }}">

            <div class="flex justify-between items-center">
                <div class="flex gap-2 items-center">
                    <flux:icon name="{{ $status['icon'] }}" class="size-5"></flux:icon>
                    <flux:heading size="lg">{{ ucfirst($type) }}</flux:heading>
                </div>

                @if (auth()->user()->role !== 'member')
                    <flux:button icon="plus" size="sm" variant="ghost" class="cursor-pointer"
                        tooltip="Add new task"
                        wire:click="create_task('{{ $type }}', '{{ $tasks->first()->project->id ?? '1' }}')"
                        wire:target="create_task('{{ $type }}')"></flux:button>
                @endif
            </div>

            {{-- Column --}}
            <div id="column-{{ $type }}"
                class="group flex flex-col gap-4 overflow-y-auto pr-2 custom-scrollbar pb-40 scroll-smooth">

                {{-- Task --}}
                @foreach ($tasks->where('status', $type) as $task)
                    <livewire:task.kanban.task :task="$task" wire:key="task-{{ $task->id }}" />
                @endforeach

                {{-- Create new task button --}}
                @if (auth()->user()->role !== 'member')
                    @if ($statuses[$type]['creating_new'])
                        <livewire:task.kanban.create-task :status="$type" :project="$project" />
                    @else
                        <flux:card
                            wire:click="create_task('{{ $type }}', '{{ $tasks->first()->project->id ?? '1' }}')"
                            {{--
                                The button uses opacity-0 to remain hidden until the parent group is hovered.
                                Pointer events are disabled when hidden to prevent phantom clicks.
                                The group-hover utility restores visibility and interactivity.
                            --}}
                            class="opacity-0 group-hover:opacity-70 pointer-events-none group-hover:pointer-events-auto flex flex-col items-center justify-center border-dashed border-2 text-zinc-50 hover:bg-zinc-50 dark:hover:bg-zinc-700 cursor-pointer hover:opacity-100 transition min-h-25">
                            <div class="relative flex flex-col items-center justify-center">
                                <flux:icon.plus wire:loading.remove
                                    wire:target="create_task('{{ $type }}', '{{ $tasks->first()->project->id ?? '1' }}')"
                                    class="size-5" />

                                <flux:icon.loading wire:loading
                                    wire:target="create_task('{{ $type }}', '{{ $tasks->first()->project->id ?? '1' }}')"
                                    class="size-5 animate-spin" />

                                <flux:text class="mt-2">New task</flux:text>
                            </div>
                        </flux:card>
                    @endif
                @endif
            </div>
        </flux:card>
    @endforeach

    @teleport('body')
        <div>
            <flux:modal name="task-settings-new">
                <livewire:task.modals.task-settings :task="$tasks->sortByDesc('id')->first()" />
            </flux:modal>

            <flux:modal name="create-tag">
                <livewire:tag.create-tag :project="$project" />
            </flux:modal>
        </div>
    @endteleport
</div>
