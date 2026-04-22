<?php

use Livewire\Component;
use App\Models\Tag;
use Flux\Flux;
use Illuminate\Validation\Rule;
use App\Models\Task;

new class extends Component {
    // Prop from parent
    public Tag $tag;
    public Task $task;

    public string $tag_name;
    public string $tag_color = '#71717a';

    public function mount(): void
    {
        $this->tag_name = $this->tag->name;
        $this->tag_color = $this->tag->hex_color;
    }

    public function update_tag()
    {
        $this->validate([
            'tag_name' => ['required', 'string', 'max:30', Rule::unique('tags', 'name')->where('project_id', $this->tag->project_id)->ignore($this->tag->id)],
            'tag_color' => 'required|regex:/^#[0-9a-fA-F]{6}$/|size:7',
        ]);

        $this->tag->update([
            'name' => $this->tag_name,
            'hex_color' => $this->tag_color,
        ]);

        $this->dispatch('tag-updated');
        Flux::toast(variant: 'success', heading: 'Tag updated!', text: "Your tag \"{$this->tag_name}\" has been updated successfully.");
        Flux::modal('tag-settings-' . $this->tag->id . '-' . $this->task->id)->close();
    }
};
?>

<div class="space-y-6">
    <div>
        <flux:heading size="lg">Edit tag</flux:heading>
    </div>

    <flux:field>
        <flux:label badge="Max: 30">Tag name</flux:label>

        <flux:input placeholder="Documentation, Database, etc." wire:model='tag_name' />

        <flux:error name="tag_name" />
    </flux:field>

    <flux:field>
        <flux:label>Tag color</flux:label>

        <div x-data="{
            color: $wire.entangle('tag_color'),
            presets: ['#71717a', '#ef4444', '#f97316', '#eab308', '#22c55e', '#3b82f6', '#6366f1', '#8b5cf6'],
        }" class="flex flex-col gap-3">

            {{-- Swatch + hex input --}}
            <div class="flex items-center gap-2">
                <label
                    class="relative h-10 w-10 flex-shrink-0 cursor-pointer rounded-lg border border-zinc-200 shadow-sm dark:border-zinc-700 overflow-hidden"
                    :style="{ backgroundColor: color }">
                    <input type="color" class="absolute inset-0 cursor-pointer opacity-0" x-model="color" />
                </label>

                <flux:input x-model="color" placeholder="#ffffff" class="font-mono" />
            </div>

            {{-- Preset swatches --}}
            <div class="flex flex-wrap gap-2">
                <template x-for="preset in presets" :key="preset">
                    <button type="button" class="h-7 w-7 cursor-pointer rounded-md border-2 shadow-sm transition-all"
                        :style="{ backgroundColor: preset }"
                        :class="color === preset ?
                            'border-zinc-900 dark:border-white scale-110' :
                            'border-transparent hover:scale-105'"
                        @click="color = preset">
                    </button>
                </template>
            </div>
        </div>

        <flux:error name="tag_color" />
    </flux:field>

    <div class="flex justify-between">
        <flux:modal.close>
            <flux:button variant="ghost">Cancel</flux:button>
        </flux:modal.close>

        <flux:button variant="primary" wire:click='update_tag'>Save changes</flux:button>
    </div>
</div>
