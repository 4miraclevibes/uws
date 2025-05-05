<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/service/store', [ServiceController::class, 'store']);
Route::get('/service', [ServiceController::class, 'index']);

// Route::post('/document', [ServiceController::class, 'storeDocument']);
// Route::get('/documents', [ServiceController::class, 'getDocument']);

// Route::post('/vidio', [ServiceController::class, 'storeVidio']);
// Route::get('/vidios', [ServiceController::class, 'getVidio']);
