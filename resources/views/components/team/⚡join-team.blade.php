<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Team;
use Illuminate\Pagination\LengthAwarePaginator;
use Flux\Flux;

new class extends Component {
    use WithPagination;

    public string $team_name = '';

    #[\Livewire\Attributes\Computed]
    public function teams()
    {
        $teams = Team::with('users')->orderBy('name')->get();

        if (empty($this->team_name)) {
            $filtered = $teams;
        } else {
            $searchTerm = strtolower($this->team_name);

            $filtered = $teams->filter(fn($team) => str_contains(strtolower($team->name), $searchTerm))->sort(function ($a, $b) use ($searchTerm) {
                $scoreA = $this->calculateSearchScore($a->name, $searchTerm);
                $scoreB = $this->calculateSearchScore($b->name, $searchTerm);
                return $scoreA <=> $scoreB;
            });
        }

        $page = request()->query('page', 1);
        $perPage = 6;
        $total = $filtered->count();
        $items = $filtered->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path' => url()->current(),
                'query' => request()->query(),
            ]
        );
    }

    private function calculateSearchScore(string $teamName, string $searchTerm): int
    {
        $name = strtolower($teamName);

        if ($name === $searchTerm) {
            return 1;
        }

        if (str_starts_with($name, $searchTerm)) {
            return 2;
        }

        return 3;
    }

    public function joinTeam($teamId)
    {
        auth()->user()->update(['team_id' => $teamId, 'role' => 'member']);

        $team = auth()->user()->team;

        Flux::toast(
            variant: 'success',
            heading: 'Joined Team',
            text: "Welcome to " . $team->name . "!"
        );

        $this->redirect(route('team'), navigate: true);
    }
};
?>

<div class="space-y-6">
    <div>
        <flux:heading size="lg">Join team</flux:heading>
        <flux:text class="mt-2">Search for existing teams to join</flux:text>
    </div>

    <flux:input icon="magnifying-glass" placeholder="Team name" wire:model.live="team_name" />

    @if ($this->teams->count() > 0)
        <flux:table :paginate="$this->teams">
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Team Size</flux:table.column>
                <flux:table.column>Owner</flux:table.column>
                <flux:table.column>Action</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->teams as $team)
                    <flux:table.row :key="$team->id">
                        <flux:table.cell>{{ $team->name }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex flex-wrap gap-2">
                                @if (count($team->users) > 0)
                                    @php
                                        $memberCount = count($team->users);
                                        if ($memberCount <= 3) {
                                            $color = 'blue';
                                        } elseif ($memberCount <= 6) {
                                            $color = 'amber';
                                        } else {
                                            $color = 'red';
                                        }
                                    @endphp
                                    <flux:badge :color="$color" size="sm">{{ $memberCount }} Members</flux:badge>
                                @else
                                    <flux:text size="sm" class="text-gray-500">No members</flux:text>
                                @endif
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>{{ $team->owner?->name ?? 'N/A' }}</flux:table.cell>

                        <flux:table.cell>
                            <flux:button size="sm" wire:click="joinTeam({{ $team->id }})" class="cursor-pointer">Join</flux:button>
                        </flux:table.cell>

                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @else
        @if (!empty($this->team_name))
            <flux:callout class="text-center py-4">
                <flux:heading size="lg">Oops!</flux:heading>
                <flux:text>No teams found matching "{{ $this->team_name }}"</flux:text>
            </flux:callout>
        @endif
    @endif
</div>
