<?php

use App\Http\Controllers\Api\MapController;
use Illuminate\Support\Facades\Route;

Route::get('/map/issues', [MapController::class, 'issues'])->name('api.map.issues');
Route::get('/map/heatmap', [MapController::class, 'heatmap'])->name('api.map.heatmap');

