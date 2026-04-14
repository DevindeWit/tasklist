<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div>
        <flux:heading size="xl">Hmm...</flux:heading>
        <flux:subheading>Seems a bit empty here!</flux:subheading>
    </div>

    <div class="flex flex-col gap-4">
        @if (auth()->user()->role === 'member')
            <flux:text>Your team manager will be able to create a new project.</flux:text>

            <flux:text>Your team's manager:</flux:text>
            <flux:profile name="{{ auth()->user()->team->owner->name }}" class="w-fit"></flux:profile>
        @endif
    </div>


    <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
    </div>
</div>
