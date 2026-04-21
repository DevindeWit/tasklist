<?php

use Livewire\Component;
use Flux\Flux;
use App\Models\Task;
use App\Models\Tag;

new class extends Component {
    public Task $task;
    public $new_data = [];

    public function save_changes()
    {
        $this->validate([
            'new_data.title' => 'required|string|min:3|max:255',
            'new_data.description' => 'nullable|string|max:1000',
            'new_data.due_date' => 'nullable|date',
        ]);

        if ($this->new_data['estimate_minutes'] <= 0) {
            $this->new_data['estimate_minutes'] = null;
        }

        $this->task->tags()->sync(
            collect($this->new_data['tags'])
                ->mapWithKeys(
                    fn($id) => [
                        $id => ['added_by' => auth()->id()],
                    ],
                )
                ->all(),
        );

        $this->task->update(array_diff_key($this->new_data, ['tags' => null]));

        Flux::modals()->close();
        Flux::toast(variant: 'success', heading: 'Task updated successfully!', text: $this->new_data['title']);

        $this->redirect(route('tasks.index', ['project_code' => $this->task->project->code]), navigate: true);
    }

    public function toggleTag(int $tagId)
    {
        if (in_array($tagId, $this->new_data['tags'])) {
            $this->new_data['tags'] = array_values(array_filter($this->new_data['tags'], fn($id) => $id !== $tagId));
        } else {
            $this->new_data['tags'][] = $tagId;
        }
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
            'tags' => $this->task->tags->pluck('id')->toArray(),
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
        <flux:field x-data="{ dateError: false }">
            <flux:label>Due date</flux:label>

            <flux:input type="date" wire:model="new_data.due_date" class="cursor-pointer appearance-none"
                x-on:change="
            const today = new Date().toISOString().split('T')[0];
            dateError = $event.target.value < today;
        " />

            <p x-show="dateError" class="text-sm text-yellow-500">
                Heads up! The selected date is before today.
            </p>
        </flux:field>

        {{-- Estimate time --}}
        <flux:field>
            <flux:label>Estimate minutes</flux:label>
            <flux:input type="number" step="15" min="0" wire:model.change='new_data.estimate_minutes' />
        </flux:field>
    </div>

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
                    <flux:card class="!w-full !min-w-0 p-0 rounded-lg cursor-pointer">
                        <div class="-m-[1px]">
                            <flux:profile class="!w-full !min-w-0 truncate rounded-lg cursor-pointer"
                                name="{{ $selectedUser?->name }}" />
                        </div>
                    </flux:card>
                </div>
            </div>

            <flux:menu class="space-y-1.5" x-data="{
                search: '',
                isMatch(name) {
                    const term = this.search.toLowerCase().trim();
                    return term === '' || name.toLowerCase().includes(term);
                }
            }">
                <flux:input class="p-1" icon-trailing="magnifying-glass" x-model="search"
                    x-on:input="search = $event.target.value" @keydown.stop="" @click.stop=""
                    placeholder="Search users..." />

                @if ($selectedUser)
                    <flux:profile name="{{ $selectedUser->name }}" class="w-full cursor-pointer" icon:trailing="x-mark"
                        wire:click="$set('new_data.assignee_id', null)" />
                    <flux:separator />
                @endif

                <div class="max-h-64 overflow-y-auto">
                    @foreach ($teamUsers->filter(fn($u) => $u->id !== $assigneeId) as $user)
                        <div wire:key="wrapper-user-{{ $user->id }}" x-show="isMatch(@js($user->name))">
                            <flux:profile name="{{ $user->name }}" class="w-full cursor-pointer"
                                :chevron="false" wire:click="$set('new_data.assignee_id', {{ $user->id }})" />
                        </div>
                    @endforeach
                </div>
            </flux:menu>
        </flux:dropdown>
    </flux:field>



    {{-- Select or create new tags --}}
    <flux:field>
        <flux:label>Tags</flux:label>

        @php
            $allTags = $task->project->tags;
            $selectedTagIds = $new_data['tags'] ?? [];
            $selectedTags = $allTags->whereIn('id', $selectedTagIds);
        @endphp

        <flux:card class="!w-full !min-w-0 p-1.5 rounded-lg flex flex-wrap gap-2">
            @foreach ($selectedTags as $tag)
                @php
                    $hex = ltrim($tag->hex_color, '#');

                    $r = hexdec(substr($hex, 0, 2));
                    $g = hexdec(substr($hex, 2, 2));
                    $b = hexdec(substr($hex, 4, 2));

                    $brightness = 0.299 * $r + 0.587 * $g + 0.114 * $b;

                    $textColor = $brightness > 150 ? '#000000' : '#FFFFFF';
                @endphp

                <flux:badge size="lg"
                    style="background-color: color-mix(in srgb, {{ $tag->hex_color }} 70%, transparent); color: {{ $textColor }};">
                    <b>{{ $tag->name }}</b>
                    <flux:badge.close class="cursor-pointer" wire:click="toggleTag({{ $tag->id }})" />
                </flux:badge>
            @endforeach

            <flux:dropdown>
                <flux:button variant="ghost" class="cursor-pointer" icon="plus" size="sm">Add tag</flux:button>

                <flux:menu keep-open class="space-y-1.5" x-data="{
                    search: '',
                    isMatch(name) {
                        const term = this.search.toLowerCase().trim();
                        return term === '' || name.toLowerCase().includes(term);
                    }
                }">
                    <flux:input icon-trailing="magnifying-glass" x-model="search" @keydown.stop="" @click.stop=""
                        placeholder="Search tags..." />

                    <flux:modal.trigger :name="'create-tag-' . $task->id">
                        <flux:menu.item icon="plus" class="cursor-pointer w-full">New tag</flux:menu.item>
                    </flux:modal.trigger>

                    <flux:separator class="my-1.5" />

                    <div class="max-h-64 overflow-y-auto">
                        @foreach ($allTags->whereNotIn('id', $selectedTags->pluck('id')) as $tag)
                            <div x-show="isMatch(@js($tag->name))">

                                <div wire:click="toggleTag({{ $tag->id }})" {{-- group for the hover trigger, flex for alignment, plus standard menu item styling --}}
                                    class="group flex items-center justify-between w-full px-2 py-1.5 cursor-pointer rounded-md hover:bg-zinc-100 dark:hover:bg-white/10 transition-colors">
                                    @php
                                        $hex = ltrim($tag->hex_color, '#');
                                        $r = hexdec(substr($hex, 0, 2));
                                        $g = hexdec(substr($hex, 2, 2));
                                        $b = hexdec(substr($hex, 4, 2));

                                        $brightness = 0.299 * $r + 0.587 * $g + 0.114 * $b;
                                        $textColor = $brightness > 150 ? '#000000' : '#FFFFFF';
                                    @endphp

                                    <flux:badge
                                        style="background-color: color-mix(in srgb, {{ $tag->hex_color }} 70%, transparent); color: {{ $textColor }};">
                                        {{ $tag->name }}
                                    </flux:badge>

                                    {{-- Trigger stays invisible until the parent div is hovered --}}
                                    <flux:modal.trigger :name="'tag-settings-' . $tag->id . '-' . $task->id"
                                        @click.stop>
                                        <flux:button variant="ghost" icon="cog-6-tooth" icon:variant="outline" size="sm"
                                            class="invisible group-hover:visible cursor-pointer" />
                                    </flux:modal.trigger>
                                </div>





                                {{--
                                <flux:menu.checkbox wire:click="toggleTag({{ $tag->id }})" class="flex py-0.5">

                                    @php
                                        $hex = ltrim($tag->hex_color, '#');

                                        $r = hexdec(substr($hex, 0, 2));
                                        $g = hexdec(substr($hex, 2, 2));
                                        $b = hexdec(substr($hex, 4, 2));

                                        $brightness = 0.299 * $r + 0.587 * $g + 0.114 * $b;

                                        $textColor = $brightness > 150 ? '#000000' : '#FFFFFF';
                                    @endphp

                                    <flux:badge
                                        style="background-color: color-mix(in srgb, {{ $tag->hex_color }} 70%, transparent); color: {{ $textColor }};">
                                        <b>{{ $tag->name }}</b>
                                    </flux:badge>

                                    <flux:spacer />

                                    <flux:modal.trigger :name="'tag-settings-' . $tag->id . '-' . $task->id"
                                        @click.stop="">
                                        <flux:button variant="ghost" icon="cog-6-tooth" icon:variant="outline"
                                            size="sm" class="cursor-pointer" />
                                    </flux:modal.trigger>

                                </flux:menu.checkbox>
                                --}}

                            </div>
                        @endforeach
                    </div>
                </flux:menu>
            </flux:dropdown>
        </flux:card>
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
        <div>
            <flux:modal :name="'delete-task-' . ($task->id ?? 'new')">
                <livewire:task.delete-task :task_id="$task->id ?? 'new'" wire:key="delete-task-{{ $task->id ?? 'new' }}" />
            </flux:modal>

            <flux:modal :name="'create-tag-' . $task->id">
                <livewire:tag.create-tag />
            </flux:modal>



            @php
                $allTags = $task->project->tags;
            @endphp

            @foreach ($allTags as $tag)
                <flux:modal :name="'tag-settings-' . $tag->id . '-' . $task->id"
                    wire:key="tag-settings-{{ $tag->id }}">
                    <livewire:tag.tag-settings :tag="$tag" wire:key="tag-settings-component-{{ $tag->id }}" />
                </flux:modal>
            @endforeach
        </div>
    @endteleport
</div>
