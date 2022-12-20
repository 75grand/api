<?php

use App\Http\Controllers\LinkController;
use Illuminate\Support\Facades\Route;

Route::get('/redirect/{link}', [LinkController::class, 'redirect'])->name('redirect');