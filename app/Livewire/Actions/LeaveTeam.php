<?php

namespace App\Livewire\Actions;

use Livewire\Component;

class LeaveTeam extends Component
{
    public function leaveTeam()
    {
        auth()->user()->update(['team_id' => null]);
    }

    public function render()
    {
        return view('components.team.leave-team');
    }
}
