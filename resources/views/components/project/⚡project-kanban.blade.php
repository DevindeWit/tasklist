<?php

use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component {
    // retrieved from parent
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
};
?>

<div x-data
    @scroll-to-bottom.window="
        // Defer layout calculations until Livewire finishes morphing and the browser paints
        setTimeout(() => {
            requestAnimationFrame(() => {
                let col = document.getElementById('column-' + $event.detail.column);
                let card = document.getElementById('kanban-' + $event.detail.column + '-' + $event.detail.board);

                if (col) {
                    col.scrollTo({
                        top: col.scrollHeight,
                        behavior: 'smooth'
                    });
                }

                if (card) {
                    let container = card.parentElement;
                    container.scrollTo({
                        left: card.offsetLeft - (container.clientWidth / 2) + (card.clientWidth / 2),
                        behavior: 'smooth'
                    });
                }
            });
        }, 50)
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

            <div class="flex flex-col gap-4 overflow-y-auto pr-2 custom-scrollbar pb-40 scroll-smooth"
                id="column-{{ $type }}">
                @foreach ($tasks->where('status', $type) as $task)
                    <livewire:project.project-kanban-task :task="$task" wire:key="task-{{ $task->id }}" />
                @endforeach

                @if (auth()->user()->role !== 'member')
                    @if ($statuses[$type]['creating_new'])
                        <livewire:task.create-task />
                    @else
                        <flux:card
                            wire:click="create_task('{{ $type }}', '{{ $tasks->first()->project->id ?? '1' }}')"
                            wire:target="create_task('{{ $type }}', '{{ $tasks->first()->project->id ?? '1' }}')"
                            class="flex flex-col items-center justify-center border-dashed border-2 text-zinc-50 hover:bg-zinc-50 dark:hover:bg-zinc-700 cursor-pointer opacity-70 hover:opacity-100 transition min-h-[100px]">
                            {{-- Container to maintain size while swapping icons --}}
                            <div class="relative flex flex-col items-center justify-center">

                                {{-- Show plus by default, hide when this specific action is loading --}}
                                <flux:icon.plus wire:loading.remove
                                    wire:target="create_task('{{ $type }}', '{{ $tasks->first()->project->id ?? '1' }}')"
                                    class="size-5" />

                                {{-- Show loading ONLY when this specific column's action is loading --}}
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
</div>
