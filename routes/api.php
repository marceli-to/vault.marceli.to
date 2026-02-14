<?php

use App\Http\Controllers\Api\EntryApiController;
use App\Http\Middleware\ValidateApiToken;
use Illuminate\Support\Facades\Route;

Route::middleware(ValidateApiToken::class)->group(function () {
    Route::get('/entries', [EntryApiController::class, 'index']);
    Route::post('/entries', [EntryApiController::class, 'store']);
    Route::get('/entries/{entry}', [EntryApiController::class, 'show']);
    Route::delete('/entries/{entry}', [EntryApiController::class, 'destroy']);
});
