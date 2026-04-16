<?php

use Livewire\Component;

new class extends Component {
    // retrieved from parent
    public $task;
};
?>

<flux:card class="space-y-6">
    <flux:heading size="lg">{{ $task->title }}</flux:heading>

    <div class="bg-zinc-900/20 p-4 rounded-lg max-h-50 overflow-y-auto custom-scrollbar">
        {!! $task->description_md !!}
    </div>
</flux:card>
