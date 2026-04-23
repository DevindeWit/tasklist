<?php

use Livewire\Component;
use Livewire\Attributes\Validate;

new class extends Component {
    // Prop from parent
    #[Validate('required')]
    public string $value;

    public function updated()
    {
        $this->validate();

        $this->dispatch('field-updated', field: 'title', value: $this->value);
    }
};
?>

<flux:field>
    <flux:label badge="Required">Task title</flux:label>
    <flux:input wire:model.live.debounce.500ms="value" autocomplete="off" />
    <flux:error name="value"></flux:error>
</flux:field>
