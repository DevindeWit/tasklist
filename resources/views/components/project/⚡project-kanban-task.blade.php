<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Carbon\Carbon;

new class extends Component {
    // retrieved from parent
    public $task;

    private array $statusMap {
        get => [
            'todo'    => [   'icon' => 'information-circle',           'color' => 'text-blue-400/80'     ],
            'doing'   => [   'icon' => 'ellipsis-horizontal-circle',   'color' => 'text-yellow-400/80'   ],
            'blocked' => [   'icon' => 'x-circle',                     'color' => 'text-red-400/80'      ],
            'done'    => [   'icon' => 'check-circle',                 'color' => 'text-green-400/80'    ],
        ];
    }

    #[Computed]
    public function statusIcon(): string
    {
        return $this->statusMap[$this->task->status]['icon'] ?? 'question-mark-circle';
    }

    #[Computed]
    public function statusColor(): string
    {
        return $this->statusMap[$this->task->status]['color'] ?? 'text-zinc-400';
    }
};
?>

<flux:card class="space-y-4 p-2">

    <div class="flex items-center gap-2 mb-0">
        <flux:icon :name="$this->statusIcon" :class="$this->statusColor" variant="outline" />

        <flux:text class="opacity-70 hover:opacity-100 transition text-xs h-fit">{{ $task->project->name }}
            #{{ $task->id }}</flux:text>

        @if (auth()->user()->role !== 'member')
            <flux:spacer />
            <flux:button icon="cog-6-tooth" icon-variant="outline" variant="ghost" size="sm" />
        @endif
    </div>

    <flux:tooltip content="{{ $task->title }}">
        <flux:heading size="lg" class="line-clamp-1 mb-4">{{ $task->title }}</flux:heading>
    </flux:tooltip>

    <div class="bg-zinc-900/40 p-2 rounded-xl border border-zinc-800/50">
        <div class="markdown-render h-50 overflow-y-auto text-sm">
            {!! $task->description_md !!}
        </div>
    </div>

    <div class="flex items-center overflow-x-auto custom-scrollbar">

        @if (!empty($task->due_date))
            <div class="flex gap-2 items-center">
                <flux:text>Due at:</flux:text>

                <flux:tooltip
                    content="{{ Carbon::parse($task->due_date)->isSameDay(now())
                        ? 'Today'
                        : \Carbon\Carbon::parse($task->due_date)->diffForHumans() }}">
                    <flux:badge class="text-xs pl-2">{{ $task->due_date }}</flux:badge>
                </flux:tooltip>
            </div>
        @endif

        <flux:spacer></flux:spacer>

        <flux:profile name="{{ $task->assignee->name }}" :chevron="false" class="pointer-events-none">
        </flux:profile>
    </div>
</flux:card>
