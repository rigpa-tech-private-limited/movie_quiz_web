<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\QuestionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'verified'])->get('/', [DashboardController::class, 'index']);
Route::middleware(['auth:sanctum', 'verified'])->get('/register', [DashboardController::class, 'index']);
Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::middleware(['auth:sanctum', 'verified'])->get('/categories', [CategoryController::class, 'index'])->name('categories');
Route::middleware(['auth:sanctum', 'verified'])->get('/questions', [QuestionController::class, 'index'])->name('questions');

