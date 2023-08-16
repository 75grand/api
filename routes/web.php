<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\MobileAuthController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', 'https://github.com/75grand/api', 301);

Route::get('/auth/callback', [MobileAuthController::class, 'callback'])->name('auth.callback');

Route::get('/auth/login', [AdminAuthController::class, 'redirect'])->name('auth.browser.redirect');
Route::get('/auth/browser-callback', [AdminAuthController::class, 'callback'])->name('auth.browser.callback');

Route::permanentRedirect('/redirect/reserve', 'https://ems.macalester.edu');
Route::permanentRedirect('/redirect/library-catalog', 'https://macalester.on.worldcat.org/discovery');
Route::permanentRedirect('/redirect/print', 'https://macalester.us.uniflowonline.com/#StartPrinting');
Route::permanentRedirect('/redirect/moodle', 'https://moodle.macalester.edu/login/index.php?authCAS=CAS');
Route::permanentRedirect('/redirect/time-clock', 'https://cas.tcplusondemand.com/43341/App_Redirect/webclock.aspx');