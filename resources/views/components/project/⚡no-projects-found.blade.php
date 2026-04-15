<?php

use Livewire\Component;
use Flux\Flux;

new class extends Component {
    public function copy_manager()
    {
        $email = auth()->user()->team->owner->email;

        $this->dispatch('copy-to-clipboard', email: $email);

        Flux::toast(heading: 'Email copied', variant: 'success', text: auth()->user()->team->owner->name . "'s email has been copied to your clipboard!");
    }

    public function go_back()
    {
        $this->redirect(route('team'));
    }
};
?>

<div class="flex flex-col gap-4 h-full text-center
            md:text-left">
    <div>
        <flux:heading size="xl">Hmm...</flux:heading>
        <flux:subheading>Seems a bit empty here!</flux:subheading>
    </div>

    <flux:callout class="text-left w-md max-w-full mx-auto md:mx-0">
        @if (auth()->user()->role === 'member')
            <flux:callout.heading icon="user" class="relative">Contact your manager!</flux:callout.heading>
            <flux:callout.text>Your current role lacks permissions to create projects. Contact your team manager for
                them to create a new project</flux:callout.text>

            <div class="flex justify-between">
                <flux:tooltip content="{{ auth()->user()->team->owner->email }}" position="bottom">
                    <flux:profile :name="auth()->user()->team->owner->name" class="w-fit" icon:trailing=""
                        wire:click='copy_manager' />
                </flux:tooltip>

                <flux:button variant="ghost" wire:click='go_back'>
                    Back
                </flux:button>
            </div>
        @else
            <flux:callout.heading icon="folder-plus">Create new project!</flux:callout.heading>
            <flux:callout.text>Projects are a great way for your entire team to manage multiple groups of tasks! Without
                a project, there can't be any tasks for your team to work on.</flux:callout.text>

            <div class="flex justify-between">
                <flux:modal.trigger name="create_project">
                    <flux:button variant="primary">
                        Create new project
                    </flux:button>
                </flux:modal.trigger>

                <flux:button variant="ghost" wire:click='go_back'>
                    Nevermind
                </flux:button>
            </div>
        @endif
    </flux:callout>

    <div class="relative flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
    </div>

    @teleport('body')
        <flux:modal name="create_project">
            <livewire:project.create-project />
        </flux:modal>
    @endteleport

    <script>
        window.addEventListener('copy-to-clipboard', event => {
            const email = event.detail.email;

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(email);
            } else {
                const input = document.createElement('input');
                input.value = email;
                document.body.appendChild(input);
                input.select();
                document.execCommand('copy');
                document.body.removeChild(input);
            }
        });
    </script>
</div>
