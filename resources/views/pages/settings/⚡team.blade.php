<?php

use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Team settings')] class extends Component {
    //
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Team settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Team')" :subheading="__('Update the team settings')">

    </x-pages::settings.layout>

    <x-pages::settings.layout :heading="__('Danger zone!')" :subheading="__('Only go down here if you know what you\'re doing')">
        <x-team.leave-team-button />
    </x-pages::settings.layout>
</section>
