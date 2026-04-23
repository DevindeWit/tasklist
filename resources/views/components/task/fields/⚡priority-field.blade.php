<?php

use Livewire\Component;
use Livewire\Attributes\Locked;

new class extends Component {
    // Prop from parent
    public string $value;

    #[Locked]
    public array $priorities = [
        'low' => [
            'color' => 'blue',
        ],
        'normal' => [
            'color' => 'green',
        ],
        'high' => [
            'color' => 'red',
        ],
    ];

    public function togglePriority(string $priority): void
    {
        $this->value = $priority;
        $this->dispatch('field-updated', field: 'priority', value: $priority);
    }
};
?>

<flux:field>
    <flux:label>Priority</flux:label>

    <flux:dropdown>
        <flux:button class="cursor-pointer w-full" icon:variant="outline" wire:loading.attr='disabled'>

            <flux:badge color="{{ $priorities[$value]['color'] }}">{{ $value }}</flux:badge>

            <flux:icon name="chevron-down" class="size-4 ml-auto" />
        </flux:button>

        <flux:menu>
            @foreach ($priorities as $priority => $info)
                <flux:menu.item wire:click="togglePriority('{{ $priority }}')" class="flex gap-2">
                    <flux:badge color="{{ $info['color'] }}">{{ $priority }}</flux:badge>
                </flux:menu.item>
            @endforeach
        </flux:menu>
    </flux:dropdown>
</flux:field>
