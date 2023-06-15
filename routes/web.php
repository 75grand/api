<?php

use App\Http\Controllers\MobileAuthController;
use App\Jobs\RefreshCalendars;
use App\Jobs\RefreshSportsCalendar;
use Illuminate\Support\Facades\Route;

Route::redirect('/', 'https://github.com/75grand/api', 301);

Route::get('/auth/callback', [MobileAuthController::class, 'callback'])->name('auth.callback');