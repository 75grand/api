<?php

use App\Http\Controllers\CalendarController;
use Illuminate\Http\Request;
use App\Http\Controllers\HoursController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NewsSource;
use App\Http\Controllers\TransitController;
use Illuminate\Support\Facades\Route;

Route::get('hours', HoursController::class);
Route::get('news/{source}', NewsController::class)->whereIn('source', array_column(NewsSource::cases(), 'value'));
Route::get('calendar', [CalendarController::class, 'list'])->name('calendar');
Route::get('calendar/{id}/image', [CalendarController::class, 'image'])->whereNumber('id')->name('calendar.image');
Route::get('transit', TransitController::class);
Route::get('map', [MapController::class, 'list']);

Route::get('menu/{date}', [MenuController::class, 'list'])->where('date', '\d{4}-\d{2}-\d{2}');
// Route::get('menu/item/{id}', [MenuController::class, 'item'])->whereNumber('id');

Route::middleware('auth:sanctum')->get('user', function (Request $request) {
    return $request->user();
});