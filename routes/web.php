<?php

declare(strict_types=1);

use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\PortfolioController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PortfolioController::class, 'home']);
Route::get('/projects/{slug}', [PortfolioController::class, 'project'])->name('projects.show');
Route::get('/up', HealthCheckController::class);
Route::get('/sitemap.xml', [PortfolioController::class, 'sitemap']);
