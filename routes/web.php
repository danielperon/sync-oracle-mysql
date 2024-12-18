<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EntryController;
use App\Http\Controllers\EntryCheckController;
use App\Http\Controllers\DataTransferController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/entries', [EntryController::class, 'index']);

Route::get('/check-entries', [EntryCheckController::class, 'checkEntries']);

Route::get('/transfer-data', [DataTransferController::class, 'transfer']);

