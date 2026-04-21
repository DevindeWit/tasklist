<?php

use Livewire\Component;
use App\Models\Tag;

new class extends Component
{
    public Tag $tag;
};
?>

<div>
    {{ $tag->name ?? '' }}
</div>
