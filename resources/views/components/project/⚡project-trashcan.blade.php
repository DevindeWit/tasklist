<?php

use Livewire\Component;

new class extends Component {
    public $projects;

    public function mount()
    {
        $this->projects = auth()->user()->team->projects->where('status', 'archived');
    }
};
?>

<div class="space-y-6">
    <div>
        <flux:heading size="lg">Deleted projects</flux:heading>
        <flux:text class="mt-2">All the projects that are marked as deleted:</flux:text>
    </div>

    <div
        class="border border-solid border-zinc-600
            p-2
            rounded-xl
            flex flex-col gap-2
            max-h-100
            overflow-y-auto
            custom-scrollbar
        ">
        @foreach ($projects as $project)
            <livewire:project.project-card :project="$project" />
        @endforeach

        @if ($projects->isEmpty())
            <flux:text class="p-10 text-center">Nothing here...</flux:text>
        @endif
    </div>
</div>
