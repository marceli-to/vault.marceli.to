<?php

use App\Http\Controllers\Api\EntryApiController;
use App\Http\Controllers\Api\TaskApiController;
use App\Http\Controllers\Api\TimeEntryApiController;
use App\Http\Middleware\ValidateApiToken;
use Illuminate\Support\Facades\Route;

Route::middleware(ValidateApiToken::class)->group(function () {
  Route::get('/entries', [EntryApiController::class, 'index']);
  Route::post('/entries', [EntryApiController::class, 'store']);
  Route::get('/entries/{entry}', [EntryApiController::class, 'show']);
  Route::delete('/entries/{entry}', [EntryApiController::class, 'destroy']);

  Route::get('/tasks', [TaskApiController::class, 'index']);
  Route::post('/tasks', [TaskApiController::class, 'store']);
  Route::get('/tasks/{task}', [TaskApiController::class, 'show']);
  Route::put('/tasks/{task}', [TaskApiController::class, 'update']);
  Route::delete('/tasks/{task}', [TaskApiController::class, 'destroy']);

  Route::get('/time-entries', [TimeEntryApiController::class, 'index']);
  Route::post('/time-entries', [TimeEntryApiController::class, 'store']);
  Route::get('/time-entries/{time_entry}', [TimeEntryApiController::class, 'show']);
  Route::put('/time-entries/{time_entry}', [TimeEntryApiController::class, 'update']);
  Route::delete('/time-entries/{time_entry}', [TimeEntryApiController::class, 'destroy']);
});
