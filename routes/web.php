<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MapController as AdminMapController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ValidationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ApiDocsController;
use App\Http\Controllers\PublicMapController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', [\App\Http\Controllers\PublicController::class, 'landing'])->name('public.landing');
Route::get('/map', [PublicMapController::class, 'index'])->name('public.map');
Route::get('/about', [\App\Http\Controllers\PublicController::class, 'about'])->name('public.about');
Route::get('/api/api-docs', [ApiDocsController::class, 'index'])->name('api.docs');
Route::get('/api/api-docs/openapi.yaml', [ApiDocsController::class, 'spec'])->name('api.docs.spec');

// Auth
Route::get('/login', [LoginController::class, 'showForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin - protected
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('reports/{id}', [AdminReportController::class, 'show'])->name('reports.show');
    Route::post('reports/{id}/validate', [ValidationController::class, 'store'])->name('reports.validate');

    Route::get('map', [AdminMapController::class, 'index'])->name('map.index');
    Route::delete('map/{id}', [AdminMapController::class, 'destroy'])->name('map.destroy');
    Route::patch('map/{id}/toggle-status', [AdminMapController::class, 'toggleStatus'])->name('map.toggle_status');

    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('users/{id}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle_status');
});
