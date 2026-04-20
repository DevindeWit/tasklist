<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::livewire('/team', 'team.teams-index')->name('team');

    // Routes requiring team membership
    Route::group(['middleware' => [function ($request, $next) {
        if (!auth()->user()->team_id) {
            abort(403, 'Access denied. You must be a member of a team to access this resource.');
        }
        return $next($request);
    }]], function () {
        // Route::view('projects', 'projects')->name('projects');   OLD
        // Route::view('projects/{project}/tasks/')                 OLD
        // Route::view('tasks', 'tasks')->name('tasks');            OLD

        Route::livewire('/projects', 'project.projects-index')->name('projects');

        Route::livewire('/tasks', 'task.tasks-index')->name('tasks');
        Route::livewire('/tasks/{project_code}', 'task.tasks-index')->name('tasks.index');
    });

    Route::view('team/join', 'team.join')->name('team.join');
    Route::view('team/create', 'team.create')->name('team.create');
});

require __DIR__ . '/settings.php';
