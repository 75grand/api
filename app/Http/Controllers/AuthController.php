<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')
            ->with(['hd' => 'macalester.edu'])
            ->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch(Exception) {
            return redirect()->route('auth.redirect');
        }

        if(explode('@', $googleUser->email)[1] !== 'macalester.edu') {
            abort(401, 'Please use a @macalester.edu email address');
        }

        if(str_ends_with($googleUser->name, 'Student Organization')) {
            abort(400, 'Please use a student email address');
        }

        $user = User::updateOrCreate([
            'email' => $googleUser->email
        ], [
            'name' => $googleUser->name,
            'avatar' => $googleUser->avatar
        ]);

        auth()->login($user, true);

        return redirect()->route('home');
    }

    public function logout()
    {
        if(!auth()->check()) abort(401);
        auth()->logout();
    }
}