<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
    @if (auth()->user()->team->projects->isEmpty())
        <x-project.no-projects-found />

    @endif
</div>
