<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

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
                        'callback_url' => request('callback_url'),
                        'referral_code' => request('referral_code')
                    ])
                ])
                ->redirect()
                ->getTargetUrl()
        ];
    }

    private function validateRequest($googleUser)
    {
        abort_unless(
            request()->has('state'),
            400, 'Missing `state` parameter'
        );

        abort_unless(
            Str::endsWith($googleUser->email, '@macalester.edu')
                || $googleUser->email === 'borgersbenjamin@gmail.com',
            401, 'Please use a Macalester email address'
        );

        abort_if(
            Str::contains($googleUser->name, 'Student Org'),
            400, 'Please use a personal email account'
        );
    }

    private function getToken(User $user, string $device): string
    {
        $user->tokens()->where('name', $device)->delete();
        return $user->createToken($device)->plainTextToken;
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $this->validateRequest($googleUser);

        $data = json_decode(request('state'), true);

        $user = User::updateOrCreate([
            'email' => $googleUser->email
        ], [
            'name' => $googleUser->name,
            'avatar' => $googleUser->avatar
        ]);

        $token = $this->getToken($user, $data['device']);

        if(str_contains($data['callback_url'], '?')) {
            $callback = $data['callback_url'] . '&token=' . $token;
        } else {
            $callback = $data['callback_url'] . '?token=' . $token;
        }

        if($user->wasRecentlyCreated) {
            $callback = $callback . '&created=true';

            $webhookData = [
                'Name' => $user->name,
                'Email' => $user->email
            ];

            // Generate random referral code for user
            $user->referral_code = strtolower(Str::random(6));

            // Save referral information
            if(!empty($data['referral_code'])) {
                $referralCode = strtolower(trim($data['referral_code']));
                $invitingUser = User::firstWhere('referral_code', $referralCode);
                abort_if($invitingUser === null, 400, 'Referral code is invalid');
                $user->referrer_id = $invitingUser->id;
                $webhookData['Referred By'] = $invitingUser->name;
            }

            $user->save();

            dispatch(function() use ($user, $webhookData) {
                webhook_alert('New User', $webhookData, $user->avatar);
            })->afterResponse();
        }

        return redirect()->away($callback);
    }
}
