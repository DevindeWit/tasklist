<?php

use Livewire\Component;
use App\Models\Project;
use Livewire\Attributes\Title;

new #[Title('Tasks')] class extends Component {
    public ?Project $project;

    public function mount(?string $project_code = null): void
    {
        if ($project_code) {
            /**
             * Use firstOrFail to ensure a 404 if the code is invalid.
             * This is cleaner than findOrFail when searching non-ID columns.
             */
            $this->project = Project::where('code', $project_code)->firstOrFail();
        }
    }
};
?>

<div>
    @if (empty($project))
        <livewire:task.recent-tasks />
    @else
        <livewire:task.task-show :project="$project" />
    @endif
</div>
