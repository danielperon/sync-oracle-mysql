<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EntryController;

Route::get('/entries', [EntryController::class, 'index']);

Route::get('/test', function () {
    return 'API is working';
});
