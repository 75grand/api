<?php

use App\Http\Controllers\HoursController;
use App\Http\Controllers\LinkController;
use App\Models\Link;
use ICal\ICal;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// GET /api/links
// GET /api/events/all
// GET /api/events/featured
// GET /redirect/{id}

Route::get('/api/links', [LinkController::class, 'list']);
Route::get('/api/hours', HoursController::class);

Route::get('/redirect/{link}', [LinkController::class, 'redirect'])->name('redirect');