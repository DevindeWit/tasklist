<div class="flex flex-col gap-4">
    <div>
        <flux:heading size="xl">{{ auth()->user()->team->name }}</flux:heading>
    </div>

    @php
        $users = auth()->user()->team->users()->paginate(10);
    @endphp

    <flux:table :paginate="$users">
        <flux:table.columns>
            <flux:table.column>Name</flux:table.column>
            <flux:table.column>Email</flux:table.column>
            <flux:table.column>Role</flux:table.column>
            <flux:table.column>Assigned tasks</flux:table.column>
            <flux:table.column>Last activity</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($users as $user)
                <flux:table.row :key="$user->id">
                    <flux:table.cell>{{ $user->name }}</flux:table.cell>
                    <flux:table.cell>{{ $user->email }}</flux:table.cell>
                    <flux:table.cell>{{ ucfirst($user->role) }}</flux:table.cell>
                    <flux:table.cell>{{ $user->assignedTasks->count() }}</flux:table.cell>
                    <flux:table.cell>
                        {{ $user->lastActivity()?->diffForHumans() ?? 'Never' }}
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
