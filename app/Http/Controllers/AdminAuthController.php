<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class AdminAuthController extends Controller
{
    public function redirect()
    {
        config(['services.google.redirect' => '/auth/browser-callback']);

        return Socialite::driver('google')
            ->with(['hd' => 'macalester.edu'])
            ->redirect();
    }

    public function callback()
    {
        config(['services.google.redirect' => '/auth/browser-callback']);

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch(InvalidStateException) {
            return redirect()->route('auth.browser.redirect');
        }

        $user = User::where('email', $googleUser->email);
        abort_if($user === null, 401);

        return redirect()->route('telescope');
    }
}
