<?php

use Livewire\Component;
use Flux\Flux;

new class extends Component {
    public function mount()
    {
        if (!empty(auth()->user()->acknowledge)) {
            Flux::modal('kicked')->show();
        }
    }

    public function acknowledge()
    {
        auth()
            ->user()
            ->update(['acknowledge' => '']);
    }
};
?>

<div>
    @if (!auth()->user()?->team_id)
        <x-team.no-team-found />
    @else
        <x-team.team-overview />
    @endif

    @teleport('body')
        <flux:modal name="kicked" @close="acknowledge" @cancel="acknowledge">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Heads up!</flux:heading>

                    <flux:text class="mt-2">
                        @switch(auth()->user()->acknowledge)
                            @case('kicked')
                                We're sorry to inform that you've been removed from your team.
                            @break

                            @case('deleted')
                                We're sorry to inform that your team has been deleted.
                            @break

                            @case('role_member')
                                It looks like your role within <cell
                                    class="bg-[#fff2]! px-1 mx-1 border-solid border border-[#fff3] rounded">
                                    {{ auth()->user()->team->name }}</cell> has changed to <cell
                                    class="bg-[#fff2]! px-1 mx-1 border-solid border border-[#fff3] rounded">member</cell>.
                            @break

                            @case('role_manager')
                                It looks like your role within <cell
                                    class="bg-[#fff2]! px-1 mx-1 border-solid border border-[#fff3] rounded"></cell>
                                {{ auth()->user()->team->name }}</cell> has changed to <cell
                                    class="bg-[#fff2]! px-1 mx-1 border-solid border border-[#fff3] rounded">manager</cell>.
                            @break

                            @default
                                Your relationship with your team has changed.
                        @endswitch
                    </flux:text>

                </div>

                <flux:text variant="strong">
                    If you wish to receive more information, please contact someone within your team.
                </flux:text>

                <div class="flex gap-3 mt-6 justify-end">
                    <flux:modal.close>
                        <flux:button variant="primary" wire:click='acknowledge'>Acknowledge</flux:button>
                    </flux:modal.close>
                </div>
            </div>
        </flux:modal>
    @endteleport
</div>
