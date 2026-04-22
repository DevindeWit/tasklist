<?php

use Livewire\Component;
use App\Models\Comment;

new class extends Component {
    // Prop from parent
    public Comment $comment;
};
?>

<flux:card class="p-2 flex flex-col gap-2">
    <div class="flex justify-between items-center">
        <flux:profile name="{{ $comment->user->name }}" class="hover:!bg-transparent" :chevron="false" />
        <flux:text variant="subtle">{{ $comment->created_at->format('d-m-Y') }}</flux:text>
    </div>

    @if (!empty($comment->body))
        <div class="bg-zinc-900/40 p-2 rounded-xl border border-zinc-800/50">
            <div class="markdown-render max-h-50 overflow-y-auto text-sm">
                {!! $comment->body_md !!}
            </div>
        </div>
    @endif
</flux:card>
