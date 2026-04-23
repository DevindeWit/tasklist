<?php

use Livewire\Component;
use Livewire\Attributes\Locked;

new class extends Component {
    // Prop from parent
    public string $value;

    #[Locked]
    public array $statuses = [
        'todo' => [
            'icon' => 'document',
        ],
        'doing' => [
            'icon' => 'document-text',
        ],
        'blocked' => [
            'icon' => 'archive-box',
        ],
        'done' => [
            'icon' => 'document-check',
        ],
    ];

    public function toggleStatus(string $status): void
    {
        $this->value = $status;
        $this->dispatch('field-updated', field: 'status', value: $status);
    }
};
?>

<flux:field>
    <flux:label>Status</flux:label>

    <flux:dropdown>
        <flux:button class="cursor-pointer w-full" icon:variant="outline" wire:loading.attr='disabled'>

            <flux:icon :name="$statuses[$value]['icon']" variant="outline" class="size-4" wire:loading.remove/>
            <flux:icon.loading class="size-4" wire:loading/>

            <span>{{ $value }}</span>
            <flux:icon name="chevron-down" class="size-4 ml-auto" />
        </flux:button>

        <flux:menu>
            @foreach ($statuses as $status => $info)
                <flux:menu.item wire:click="toggleStatus('{{ $status }}')" class="flex gap-2">
                    <x-slot:icon>
                        <flux:icon :name="$info['icon']" variant="outline" class="size-4" />
                    </x-slot:icon>
                    {{ $status }}
                </flux:menu.item>
            @endforeach
        </flux:menu>
    </flux:dropdown>
</flux:field>
