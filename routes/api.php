<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TimeLogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Authentication Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Client Routes
    Route::apiResource('clients', ClientController::class);

    // Project Routes
    Route::apiResource('projects', ProjectController::class);

    // Get projects by client
    Route::get('/clients/{client}/projects', [ProjectController::class, 'index']);

    // TimeLog Routes
    Route::apiResource('time-logs', TimeLogController::class);
    Route::post('/time-logs/start', [TimeLogController::class, 'start']);
    Route::post('/time-logs/{timeLog}/stop', [TimeLogController::class, 'stop']);
    Route::get('/projects/{project}/time-logs', [TimeLogController::class, 'index']);

    // Reports
    Route::get('/reports', [TimeLogController::class, 'report']);
});
