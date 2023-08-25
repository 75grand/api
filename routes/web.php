<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\MobileAuthController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::view('/privacy', 'privacy')->name('privacy');
Route::view('/terms', 'terms')->name('terms');
Route::view('/delete-account', 'delete-account');

Route::redirect('/download/ios', 'https://apple.co/45DznpV')->name('download.ios');
Route::redirect('/download/android', 'https://play.google.com/store/apps/details?id=zone.jero.grand')->name('download.android');

Route::get('/auth/callback', [MobileAuthController::class, 'callback'])->name('auth.callback');

Route::get('/auth/login', [AdminAuthController::class, 'redirect'])->name('login');
Route::get('/auth/browser-callback', [AdminAuthController::class, 'callback'])->name('auth.browser.callback');

Route::redirect('/redirect/reserve', 'https://ems.macalester.edu');
Route::redirect('/redirect/library-catalog', 'https://macalester.on.worldcat.org/discovery');
Route::redirect('/redirect/print', 'https://macalester.us.uniflowonline.com/#StartPrinting');
Route::redirect('/redirect/moodle', 'https://moodle.macalester.edu/login/index.php?authCAS=CAS');
Route::redirect('/redirect/time-clock', 'https://cas.tcplusondemand.com/43341/App_Redirect/webclock.aspx');