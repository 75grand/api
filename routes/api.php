<?php

use App\Http\Controllers\CalendarEventController;
use App\Http\Controllers\MobileAuthController;
use App\Http\Controllers\EventAttendeeController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HoursController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NewsSource;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\MoodleController;
use App\Http\Controllers\TransitController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// API Health Check
Route::get('status', fn() => 'ok');

// Marketplace
Route::middleware('auth:sanctum')->group(function() {
    Route::apiResource('listings', ListingController::class);
});

// Course Catalog
// Route::get('courses/{term:code}', [CourseCatalogController::class, 'index']);

// Feedback
Route::post('feedback', [FeedbackController::class, 'store']);

// Building Hours
Route::get('hours', HoursController::class);

// News
Route::get('news/{source}', NewsController::class)
    ->whereIn('source', array_column(NewsSource::cases(), 'value'));

// Calendar Events
Route::get('events', [CalendarEventController::class, 'index']);
Route::get('events/{event}', [CalendarEventController::class, 'show']);

Route::middleware('auth:sanctum')->group(function() {
    Route::get('events/{event}/attendees', [EventAttendeeController::class, 'index']);
    Route::patch('events/{event}/attendees', [EventAttendeeController::class, 'update']);
});

// Public Transit
Route::get('transit', TransitController::class);

// Campus Map
Route::get('map', [MapController::class, 'index']);

// Dining Hall Menus
Route::get('menus/{date}', [MenuController::class, 'index'])->where('date', '\d{4}-\d{2}-\d{2}');
Route::get('menus/item/{id}', [MenuController::class, 'show'])->whereNumber('id');

// Authentication
Route::get('authentication', [MobileAuthController::class, 'redirect']);

// Moodle
Route::middleware('auth:sanctum')->group(function() {
    Route::get('moodle', [MoodleController::class, 'index']);
});

// User Accounts
Route::middleware('auth:sanctum')->group(function() {
    Route::get('user', [UserController::class, 'show']);
    Route::patch('user', [UserController::class, 'update']);
    Route::put('user', [UserController::class, 'update']);
});