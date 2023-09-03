<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class WidgetController extends Controller
{
    public function __invoke(Request $request)
    {
        return [
            'users' => User::count(),
            'users_enabled_notifications' => User::whereNotNull('expo_token')->count()
        ];
    }
}
