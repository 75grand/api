<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CalendarEventController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\MobileAuthController;
use App\Http\Controllers\WidgetController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::view('/privacy', 'privacy')->name('privacy');
Route::view('/terms', 'terms')->name('terms');
Route::view('/delete-account', 'delete-account');
Route::view('/support', 'support');

Route::get('/download', [DownloadController::class, 'automatic'])->name('download');
Route::get('/download/ios', [DownloadController::class, 'ios'])->name('download.ios');
Route::get('/download/android', [DownloadController::class, 'android'])->name('download.android');

Route::get('/marketplace/{listing}', [ListingController::class, 'page'])->name('listings.show');
Route::get('/calendar/{event}', [CalendarEventController::class, 'page'])->name('events.show');

Route::get('/auth/callback', [MobileAuthController::class, 'callback'])->name('auth.callback');

Route::get('/auth/login', [AdminAuthController::class, 'redirect'])->name('login');
Route::get('/auth/browser-callback', [AdminAuthController::class, 'callback'])->name('auth.browser.callback');

Route::redirect('/redirect/reserve', 'https://ems.macalester.edu');
Route::redirect('/redirect/library-catalog', 'https://macalester.on.worldcat.org/discovery');
Route::redirect('/redirect/print', 'https://macalester.us.uniflowonline.com/#StartPrinting');
Route::redirect('/redirect/moodle', 'https://moodle.macalester.edu/login/index.php?authCAS=CAS');
Route::redirect('/redirect/time-clock', 'https://43341.tcplusondemand.com/app/webclock/index.html#/EmployeeLogOn/43341');

Route::get('/api/widget-data', WidgetController::class);