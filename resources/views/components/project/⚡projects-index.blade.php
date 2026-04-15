<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div class="h-full">
    @if (auth()->user()->team->projects->isEmpty())
        <div class="h-full">
            <livewire:project.no-projects-found />
        </div>
    @endif
</div>
