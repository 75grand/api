<?php

use App\Http\Controllers\AttachEventAttendee;
use App\Http\Controllers\MobileAuthController;
use App\Http\Controllers\CourseCatalogController;
use App\Http\Controllers\SendFeedback;
use App\Http\Controllers\HoursController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NewsSource;
use App\Http\Controllers\ListEventAttendees;
use App\Http\Controllers\ListEvents;
use App\Http\Controllers\ShowEvent;
use App\Http\Controllers\TransitController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// API Health Check
Route::get('status', fn() => 'ok');

// Course Catalog
Route::get('courses/{term:code}', [CourseCatalogController::class, 'index']);

// Feedback
Route::post('feedback', SendFeedback::class);

// Building Hours
Route::get('hours', HoursController::class);

// News
Route::get('news/{source}', NewsController::class)->whereIn('source', array_column(NewsSource::cases(), 'value'));

// Calendar Events
Route::get('events', ListEvents::class);
Route::get('events/{event}', ShowEvent::class);

Route::middleware('auth:sanctum')->group(function() {
    Route::get('events/{event}/attendees', ListEventAttendees::class);
    Route::post('events/{event}/attendees', AttachEventAttendee::class);
});

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
    Route::patch('user', [UserController::class, 'update']);
    Route::put('user', [UserController::class, 'update']);
});