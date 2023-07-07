<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Nette\InvalidStateException;

class MobileAuthController extends Controller
{
    public function redirect()
    {
        abort_unless(
            request()->has(['device', 'callback_url']),
            400, 'Missing `device` or `callback_url` parameter'
        );

        return [
            'redirect_url' => Socialite::driver('google')
                ->stateless()
                ->with([
                    'hd' => 'macalester.edu',
                    'state' => json_encode([
                        'device' => request('device'),
                        'callback_url' => request('callback_url')
                    ])
                ])
                ->redirect()
                ->getTargetUrl()
        ];
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        abort_unless(
            request()->has('state'),
            400, 'Missing `state` parameter'
        );

        abort_unless(
            str_ends_with($googleUser->email, '@macalester.edu'),
            401, 'Please use a Macalester email address'
        );

        $user = User::updateOrCreate([
            'email' => $googleUser->email
        ], [
            'name' => $googleUser->name,
            'avatar' => $googleUser->avatar
        ]);

        if($user->wasRecentlyCreated) {
            dispatch(function() use ($user) {
                webhook_alert('New User', [
                    'Name' => $user->name,
                    'Email' => $user->email
                ], $user->avatar);
            })->afterResponse();
        }

        $data = json_decode(request('state'), true);

        // Delete existing tokens for this device
        $user->tokens()->where('name', $data['device'])->delete();

        // Create a new token for this device
        $token = $user->createToken($data['device'])->plainTextToken;

        if(str_contains($data['callback_url'], '?')) {
            $callback = $data['callback_url'] . '&token=' . $token;
        } else {
            $callback = $data['callback_url'] . '?token=' . $token;
        }

        return redirect()->away($callback);
    }
}
