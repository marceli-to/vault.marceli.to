<?php

use App\Http\Controllers\EntryController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TimeEntryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [EntryController::class, 'index'])->name('dashboard');
    Route::post('/entries', [EntryController::class, 'store'])->name('entries.store');
    Route::put('/entries/{entry}', [EntryController::class, 'update'])->name('entries.update');
    Route::delete('/entries/{entry}', [EntryController::class, 'destroy'])->name('entries.destroy');

    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    Route::get('/time-entries', [TimeEntryController::class, 'index'])->name('time-entries.index');
    Route::post('/time-entries', [TimeEntryController::class, 'store'])->name('time-entries.store');
    Route::put('/time-entries/{time_entry}', [TimeEntryController::class, 'update'])->name('time-entries.update');
    Route::delete('/time-entries/{time_entry}', [TimeEntryController::class, 'destroy'])->name('time-entries.destroy');
});

require __DIR__.'/auth.php';
