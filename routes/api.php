<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/image', [ServiceController::class, 'storeImage']);
Route::get('/images', [ServiceController::class, 'getImage']);

Route::post('/document', [ServiceController::class, 'storeDocument']);
Route::get('/documents', [ServiceController::class, 'getDocument']);

Route::post('/vidio', [ServiceController::class, 'storeVidio']);
Route::get('/vidios', [ServiceController::class, 'getVidio']);
