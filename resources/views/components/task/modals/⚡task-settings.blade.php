<?php

use Livewire\Component;
use App\Models\Task;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;

new class extends Component {
    #[Locked]
    public Task $task;

    #[
        Validate([
            'new_data.title' => 'required|string|max:255',
            'new_data.description' => 'nullable|string|max:1000',
            'new_data.due_date' => 'nullable|date',
        ])
    ]
    public array $new_data = [];

    public function mount(): void
    {
        $this->new_data = $this->task->toArray();
    }

    #[On('reset-fields')]
    public function resetData(): void
    {
        $this->new_data = $this->task->fresh()->toArray();
    }

    #[On('field-updated')]
    public function updateField(string $field, mixed $value): void
    {
        $this->new_data[$field] = $value;
    }

    #[On('save-data')]
    public function saveData() {
        $this->validate();
    }
};
?>

<div class="space-y-6">

    <div class="grid grid-cols-2 gap-2">
        <livewire:task.fields.status-field :value="$new_data['status']" />
        <livewire:task.fields.priority-field :value="$new_data['priority']" />
    </div>

    <livewire:task.fields.title-field :value="$new_data['title']" />

    <x-task.fields.buttons-field :task="$task" />

    {{-- testing --}}
    @foreach ($new_data as $field => $value)
        <flux:text>{{ $field }}: {{ json_encode($value) }}</flux:text>
    @endforeach
</div>
