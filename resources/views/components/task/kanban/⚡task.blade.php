<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use App\Models\Task;

new class extends Component {
    public $task;

    /**
     * Map priority levels to Flux badge color tokens.
     * PHP 8.4 property hooks keep this mapping encapsulated and read-only.
     */
    private array $priorityColorMap {
        get => [
            'low'    => 'blue',
            'normal' => 'green',
            'high'   => 'red',
        ];
    }

    /**
     * Resolve the badge color based on task priority.
     */
    #[Computed]
    public function priorityColor(): string
    {
        return $this->priorityColorMap[$this->task->priority] ?? 'zinc';
    }

    /**
     * Determines the sequence number of the task within its project.
     * This counts all project tasks created before or at the same time as this one.
     * We use a comparison on both created_at and id to ensure a stable, deterministic sort.
     */
    #[Computed]
    public function projectTaskNumber(): int
    {
        return Task::where('project_id', $this->task->project_id)
            ->where(function ($query) {
                $query->where('created_at', '<', $this->task->created_at)
                    ->orWhere(function ($query) {
                        $query->where('created_at', $this->task->created_at)
                            ->where('id', '<=', $this->task->id);
                    });
            })
            ->count();
    }
};
?>

<flux:card class="flex flex-col gap-2 p-2">

    <div class="flex md:items-center md:gap-2">
        <div class="flex flex-col md:flex-row w-full gap-2">
            <div class="flex items-center">
                <flux:text>Priority: </flux:text>
                <flux:badge :color="$this->priorityColor" size="sm" inset="top bottom" class="py-0 ml-2 capitalize">
                    {{ $task->priority }}
                </flux:badge>
            </div>

            <flux:spacer class="hidden md:inline" />

            <flux:text class="opacity-70 hover:opacity-100 transition text-xs h-fit">
                {{ $task->project->name }} #{{ $this->projectTaskNumber }}
            </flux:text>

            <flux:spacer class="hidden md:inline" />
        </div>

        @if (auth()->user()->role !== 'member')
            <flux:spacer />

            <flux:modal.trigger :name="'task-settings-' . $task->id">
                <flux:button icon="cog-6-tooth" icon-variant="outline" variant="ghost" size="sm"
                    class="cursor-pointer" />
            </flux:modal.trigger>
        @endif
    </div>

    <flux:tooltip content="{{ $task->title ?? '' }}">
        <flux:heading size="lg" class="line-clamp-1">{{ $task->title ?? '' }}</flux:heading>
    </flux:tooltip>

    @if (!empty($task->description))
        <div class="bg-zinc-900/40 p-2 rounded-xl border border-zinc-800/50">
            <div class="markdown-render max-h-50 overflow-y-auto text-sm">
                {!! $task->description_md !!}
            </div>
        </div>
    @endif

    <div class="overflow-x-auto -mx-2 p-2">
        {{-- Due date and assignee --}}
        @if (!empty($task->due_date) || !empty($task->assignee->name))
            <div class="flex items-center custom-scrollbar gap-2">

                @if (!empty($task->due_date))
                    <div class="flex gap-2 items-center">
                        <flux:text class=" text-nowrap">Due at:</flux:text>

                        <flux:tooltip
                            content="{{ Carbon::parse($task->due_date)->isSameDay(now()) ? 'Today' : Carbon::parse($task->due_date)->diffForHumans() }}">
                            <flux:badge size="sm">{{ $task->due_date }}</flux:badge>
                        </flux:tooltip>
                    </div>
                @endif

                <flux:spacer></flux:spacer>

                @if (!empty($task->assignee->name))
                    {{-- Small screens: initials --}}
                    <flux:profile initials="{{ $task->assignee->initials() }}" :chevron="false"
                        class="pointer-events-none md:hidden" />

                    {{-- md and up: full name --}}
                    <flux:profile name="{{ $task->assignee->name }}" :chevron="false"
                        class="pointer-events-none hidden md:flex" />
                @endif

            </div>
        @endif

        {{-- Estimated minutes and tags --}}
        @if (!empty($task->estimate_minutes) || !empty($task->tags))
            <div class="flex items-start custom-scrollbar gap-2 justify-between">

                @if (!empty($task->estimate_minutes))
                    <div class="flex gap-2 items-center">
                        <flux:text>Estimate:</flux:text>

                        <flux:tooltip content="{{ $task->estimate_minutes }} minutes">
                            <flux:badge size="sm">
                                {{ CarbonInterval::minutes($task->estimate_minutes)->cascade()->format('%h:%I') }}
                            </flux:badge>
                        </flux:tooltip>
                    </div>
                @endif

                <flux:spacer></flux:spacer>

                @if ($task->tags && $task->tags->isNotEmpty())
                    <div class="flex gap-2 items-center flex-wrap justify-end">
                        @foreach ($task->tags as $tag)
                            @php
                                $hex = ltrim($tag->hex_color, '#');

                                $r = hexdec(substr($hex, 0, 2));
                                $g = hexdec(substr($hex, 2, 2));
                                $b = hexdec(substr($hex, 4, 2));

                                $brightness = 0.299 * $r + 0.587 * $g + 0.114 * $b;

                                $textColor = $brightness > 150 ? '#000000' : '#FFFFFF';
                            @endphp

                            <flux:badge size="sm"
                                style="background-color: color-mix(in srgb, {{ $tag->hex_color }} 70%, transparent); color: {{ $textColor }};">
                                <b>{{ $tag->name }}</b>
                            </flux:badge>
                        @endforeach
                    </div>
                @endif

            </div>
        @endif
    </div>

    {{-- Created at --}}
    <div class="flex justify-end">
        <flux:text class="opacity-70 hover:opacity-100 transition text-xs h-fit">{{ $task->created_at }}</flux:text>
    </div>

    @teleport('body')
        <flux:modal :name="'task-settings-' . $task->id">
            <livewire:task.task-settings :task="$task" />
        </flux:modal>
    @endteleport
</flux:card>
