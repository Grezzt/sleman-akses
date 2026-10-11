<?php

use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MapController as AdminMapController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SpatialAnalysisController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ValidationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ApiDocsController;
use App\Http\Controllers\PublicArticleController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\PublicMapController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', [PublicController::class, 'landing'])->name('public.landing');
Route::get('/map', [PublicMapController::class, 'index'])->name('public.map');
Route::get('/berita', [PublicArticleController::class, 'index'])->name('public.articles.index');
Route::get('/berita/{slug}', [PublicArticleController::class, 'show'])->name('public.articles.show');
Route::get('/about', [PublicController::class, 'about'])->name('public.about');
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

    Route::get('spatial-analysis', [SpatialAnalysisController::class, 'index'])->name('spatial.index');
    Route::get('spatial-analysis/data', [SpatialAnalysisController::class, 'data'])->name('spatial.data');

    Route::get('articles', [AdminArticleController::class, 'index'])->name('articles.index');
    Route::get('articles/create', [AdminArticleController::class, 'create'])->name('articles.create');
    Route::post('articles', [AdminArticleController::class, 'store'])->name('articles.store');
    Route::get('articles/{id}', [AdminArticleController::class, 'show'])->name('articles.show');
    Route::get('articles/{id}/edit', [AdminArticleController::class, 'edit'])->name('articles.edit');
    Route::put('articles/{id}', [AdminArticleController::class, 'update'])->name('articles.update');
    Route::delete('articles/{id}', [AdminArticleController::class, 'destroy'])->name('articles.destroy');
    Route::patch('articles/{id}/toggle-status', [AdminArticleController::class, 'toggleStatus'])->name('articles.toggle_status');
    Route::post('articles/{id}/approve', [AdminArticleController::class, 'approve'])->name('articles.approve');
    Route::post('articles/{id}/reject', [AdminArticleController::class, 'reject'])->name('articles.reject');
    Route::post('articles/scrape', [AdminArticleController::class, 'scrape'])->name('articles.scrape');
    Route::post('articles/scrape-bot', [AdminArticleController::class, 'scrapeBot'])->name('articles.scrape_bot');
    Route::post('articles/scrape-ai', [AdminArticleController::class, 'scrapeAi'])->name('articles.scrape_ai');
    Route::post('articles/upload-body-image', [AdminArticleController::class, 'uploadBodyImage'])->name('articles.upload_body_image');
    Route::post('articles/bulk-action', [AdminArticleController::class, 'bulkAction'])->name('articles.bulk_action');

    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('users/{id}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle_status');
});
