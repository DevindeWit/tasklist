<div class="flex flex-col gap-4">
    <div>
        <flux:heading class="text-center" size="xl">Looks like you aren't in a team!</flux:heading>
        <flux:text class="text-center">Let's fix that - Join or create a team.</flux:text>
    </div>


    <div class="p-2 flex justify-center gap-4">
        <flux:modal.trigger name="join-team">
            <flux:button icon="magnifying-glass" variant="primary" class="cursor-pointer">
                Join
            </flux:button>
        </flux:modal.trigger>

        <flux:modal.trigger name="create-team">
            <flux:button icon="plus" class="cursor-pointer">
                Create
            </flux:button>
        </flux:modal.trigger>
    </div>

    <flux:text class="text-center" variant="">Being in a team is required to access projects and tasks</flux:text>

    <flux:modal name="join-team">
        <livewire:team.join-team />
    </flux:modal>

    <flux:modal name="create-team">
        <x-team.create-team />
    </flux:modal>
</div>
