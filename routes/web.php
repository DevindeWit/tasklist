<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::view('team', 'team')->name('team');

    // Routes requiring team membership
    Route::group(['middleware' => [function ($request, $next) {
        if (!auth()->user()->team_id) {
            abort(403, 'Access denied. You must be a member of a team to access this resource.');
        }
        return $next($request);
    }]], function () {
        Route::view('projects', 'projects')->name('projects');
        Route::view('tasks', 'tasks')->name('tasks');
    });

    Route::view('team/join', 'team.join')->name('team.join');
    Route::view('team/create', 'team.create')->name('team.create');
});

require __DIR__ . '/settings.php';
