<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky collapsible="mobile"
        class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ route('team') }}" wire:navigate />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.group :heading="__('Platform')" class="grid">

                {{-- Sidebar items --}}
                <flux:sidebar.item icon="user-circle" :href="route('team')" :current="request()->routeIs('team')"
                    wire:navigate>
                    {{ __('Team') }}
                </flux:sidebar.item>

                @if (auth()->user()->team_id)
                    <flux:sidebar.item icon="folder-open" :href="route('projects')"
                        :current="request()->routeIs('projects')" wire:navigate>
                        {{ __('Projects') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="calendar-days" :href="route('tasks')"
                        :current="request()->routeIs('tasks')" wire:navigate>
                        {{ __('Tasks') }}
                    </flux:sidebar.item>
                @else
                    <flux:sidebar.item icon="folder-open" disabled
                        class="opacity-50 cursor-not-allowed hover:bg-transparent">
                        {{ __('Projects') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="calendar-days" disabled
                        class="opacity-50 cursor-not-allowed hover:bg-transparent">
                        {{ __('Tasks') }}
                    </flux:sidebar.item>
                @endif

            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:spacer />

        @if (auth()->user()->team_id !== null)
            @php
                $role = auth()->user()->role;

                $roleClasses = match ($role) {
                    'member' => 'bg-blue-500/20 border-blue-200/30 text-blue-200',
                    'manager' => 'bg-green-500/20 border-green-200/30 text-green-200',
                    'admin' => 'bg-red-500/20 border-red-200/30 text-red-200',
                    default => 'bg-gray-500/20 border-gray-200/30 text-gray-200',
                };
            @endphp

            <flux:sidebar.item icon="shield-check" disabled class="pointer-events-none">
                Role:
                <cell class="px-1 mx-1 border-solid border rounded {{ $roleClasses }}">
                    {{ ucfirst($role) }}
                </cell>
            </flux:sidebar.item>
        @endif


        <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="adjustments-horizontal" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer" data-test="logout-button">
                        {{ __('Log out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @fluxScripts

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist
</body>

</html>
