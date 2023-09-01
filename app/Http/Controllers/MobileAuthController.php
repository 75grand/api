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

        $referralCode = Str::lower(request('referral_code'));

        if($referralCode === env('APP_STORE_REVIEW_SECRET')) {
            return [
                'redirect_url' => route('auth.callback', [
                    'is_reviewer' => true,
                    'callback_url' => request('callback_url')
                ])
            ];
        }

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

    public function callback()
    {
        $callbackUrl = request()->has('is_reviewer')
            ? $this->logInReviewer()
            : $this->logInUser();

        return redirect()->away($callbackUrl);
    }

    /**
     * Log in a regular user
     * @return string Callback URL with `token` parameter attached
     */
    private function logInUser(): string
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

        $user->tokens()->where('name', $data['device'])->delete();
        $token = $user->createToken($data['device'])->plainTextToken;

        $callback = $this->formatCallbackUrl($data['callback_url'], $token);

        if($user->wasRecentlyCreated) {
            $callback = "$callback&created=true";

            $webhookData = [
                'Name' => $user->name,
                'Email' => $user->email,
                'Device' => $data['device']
            ];

            // Save referral information
            if(!empty($data['referral_code'])) {
                $code = Str::lower($data['referral_code']);

                $referrer = User::firstWhere('referral_code', $code);
                abort_if($referrer === null, 400, 'Referral code is invalid');

                $user->referrer_id = $referrer->id;
                $webhookData['Referred By'] = $referrer->name;
            }

            // Generate random referral code for user
            $user->referral_code = Str::lower(Str::random(6));

            $user->save();

            dispatch(function() use ($user, $webhookData) {
                webhook_alert("New User: $user->name", $webhookData, $user->avatar);
            })->afterResponse();
        }

        return $callback;
    }

    /**
     * Log in an App Store or Google Play Store reviewer
     * @return string Callback URL with `token` parameter attached
     */
    private function logInReviewer(): string
    {
        dispatch(function() {
            webhook_alert('App store reviewer just logged in!');
        })->afterResponse();

        $user = User::find(env('APP_STORE_REVIEW_ACCOUNT_ID'));
        $token = $user->createToken('App Store Review')->plainTextToken;
        $callback = request('callback_url');
        
        return $this->formatCallbackUrl($callback, $token);
    }

    private function formatCallbackUrl(string $url, string $token): string
    {
        return Str::contains($url, '?')
            ? "$url&token=$token"
            : "$url?token=$token";
    }

    private function validateRequest($googleUser)
    {
        abort_unless(
            request()->has('state'),
            400, 'Missing `state` parameter'
        );

        abort_unless(
            Str::endsWith($googleUser->email, '@macalester.edu')
                || in_array($googleUser->email, [
                    'borgersbenjamin@gmail.com',
                    'eliot.supceo@gmail.com'
                ]),
            401, 'Please use a Macalester email address'
        );

        abort_if(
            Str::contains($googleUser->name, 'Student Org'),
            400, 'Please use a personal email account'
        );
    }
}
