<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\MapController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
  Route::post('auth/register', [AuthController::class, 'register']);
  Route::post('auth/login', [AuthController::class, 'login']);
  Route::get('categories', [CategoryController::class, 'index']);
  Route::get('map', [MapController::class, 'index']);
  Route::get('map/{id}', [MapController::class, 'show']);
});

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
  Route::post('auth/logout', [AuthController::class, 'logout']);
  Route::get('profile', [ProfileController::class, 'show']);
  Route::put('profile', [ProfileController::class, 'update']);
  Route::get('reports', [ReportController::class, 'index']);
  Route::post('reports', [ReportController::class, 'store']);
  Route::get('reports/{id}', [ReportController::class, 'show']);
});
