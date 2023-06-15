<?php

use App\Http\Controllers\MobileAuthController;
use App\Http\Controllers\CalendarEventController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HoursController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NewsSource;
use App\Http\Controllers\TransitController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// API Health Check
Route::get('status', fn() => 'ok');

// Feedback
Route::post('feedback', FeedbackController::class);

// Building Hours
Route::get('hours', HoursController::class);

// News
Route::get('news/{source}', NewsController::class)->whereIn('source', array_column(NewsSource::cases(), 'value'));

// Calendar Events
Route::apiResource('events', CalendarEventController::class)->only(['index', 'show']);

// Public Transit
Route::get('transit', TransitController::class);

// Campus Map
Route::get('map', [MapController::class, 'index']);

// Dining Hall Menus
Route::get('menu/{date}', [MenuController::class, 'index'])->where('date', '\d{4}-\d{2}-\d{2}');
Route::get('menu/item/{id}', [MenuController::class, 'show'])->whereNumber('id');

// Authentication
Route::get('authentication', [MobileAuthController::class, 'redirect']);

// User Accounts
Route::middleware('auth:sanctum')->group(function() {
    Route::get('user', [UserController::class, 'show']);
});