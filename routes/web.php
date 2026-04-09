<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('team', 'team')->name('team');
    Route::view('projects', 'projects')->name('projects');
    Route::view('tasks', 'tasks')->name('tasks');

    Route::view('team/join', 'team.join')->name('team.join');
    Route::view('team/create', 'team.create')->name('team.create');
});

require __DIR__ . '/settings.php';
