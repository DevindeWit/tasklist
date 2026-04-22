<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use App\Models\Task;
use App\Models\User;
use Flux\Flux;

new class extends Component {

    // Prop from parent
    public $task;

    /**
     * Map priority levels to Flux badge color tokens
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

<div class="flex flex-col gap-2 mt-8 h-[calc(100vh-var(--spacing)*24)]">

    <div class="flex md:items-center md:gap-2">
        <div class="flex flex-col md:flex-row w-full gap-2">
            <div class="flex items-center">
                <flux:text>Priority: </flux:text>
                <flux:badge :color="$this->priorityColor" size="sm" inset="top bottom" class="py-0 ml-2 capitalize">
                    {{ $task->priority }}
                </flux:badge>
            </div>

            <flux:spacer />

            <flux:text class="opacity-70 hover:opacity-100 transition text-xs h-fit">
                {{ $task->project->name }} #{{ $this->projectTaskNumber }}
            </flux:text>
        </div>
    </div>

    <flux:tooltip content="{{ $task->title ?? '' }}">
        <flux:heading size="lg" class="line-clamp-1">{{ $task->title ?? '' }}</flux:heading>
    </flux:tooltip>

    @if (!empty($task->description))
        <div class="bg-zinc-900/40 p-2 rounded-xl border border-zinc-800/50 max-w-lg">
            <div class="markdown-render overflow-y-auto max-h-100 text-sm">
                {!! $task->description_md !!}
            </div>
        </div>
    @endif

    <div class="overflow-x-auto -mx-2 p-2 bg-zinc-800/20">
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

                            <flux:tooltip>
                                <flux:badge size="sm"
                                    style="background-color: color-mix(in srgb, {{ $tag->hex_color }} 70%, transparent); color: {{ $textColor }};">
                                    <b>{{ $tag->name }}</b>
                                </flux:badge>

                                <flux:tooltip.content>
                                    Added by: {{ User::find($tag->pivot->added_by)->name }} <br>
                                    Added on: {{ $tag->pivot->updated_at->format('d-m-Y') }}
                                </flux:tooltip.content>
                            </flux:tooltip>
                        @endforeach
                    </div>
                @endif

            </div>
        @endif
    </div>

    {{-- Comments --}}
    <div class="flex-1 overflow-y-auto flex flex-col gap-2">
        @foreach ($task->comments as $comment)
            <livewire:comment.comment :comment="$comment" />
        @endforeach
    </div>

    {{-- Created at --}}
    <div class="flex justify-between items-center bg-zinc-800/20 -m-2 p-2">
        <flux:text class="opacity-70 hover:opacity-100 transition text-xs h-fit">
            Comments: {{ $task->comments->count() }}
        </flux:text>

        <flux:text class="opacity-70 hover:opacity-100 transition text-xs h-fit">
            {{ $task->created_at->format('d-m-Y') }}
        </flux:text>
    </div>
</div>
