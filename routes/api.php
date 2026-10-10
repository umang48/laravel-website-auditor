<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WebsiteController;
use App\Http\Controllers\Api\AuditController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Our new Website API endpoints
Route::apiResource('websites', WebsiteController::class);
Route::post('/websites/{website}/audits', [AuditController::class, 'store']);