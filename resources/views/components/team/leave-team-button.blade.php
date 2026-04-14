<div>
    @if (auth()->user()->id === auth()->user()->team?->owner_id)
        <flux:modal.trigger name="delete-team">
            <flux:button variant="danger">Delete Team</flux:button>
        </flux:modal.trigger>
    @else
        <flux:modal.trigger name="leave-team">
            <flux:button variant="danger">Leave Team</flux:button>
        </flux:modal.trigger>
    @endif

    @teleport('body')
        @if (auth()->user()->id === auth()->user()->team?->owner_id)
            <flux:modal name="delete-team">
                <livewire:team.delete-team-modal />
            </flux:modal>
        @else
            <flux:modal name="leave-team">
                <livewire:team.leave-team-modal />
            </flux:modal>
        @endif
    @endteleport
</div>
