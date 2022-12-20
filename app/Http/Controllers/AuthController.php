<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Nette\InvalidStateException;

class AuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')
            ->with(['hl' => 'macalester.edu'])
            ->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch(InvalidStateException) {
            return redirect()->route('auth.redirect');
        }

        $user = User::updateOrCreate([
            'email' => $googleUser->email
        ], [
            'name' => $googleUser->name,
            'avatar' => $googleUser->avatar
        ]);

        auth()->login($user, true);

        if($user->wasRecentlyCreated) {
            webhook_alert('New User', [
                'Name' => $user->name,
                'Email' => $user->email
            ], $user->avatar);
        }
    }
}
