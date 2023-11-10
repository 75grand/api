<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Jobs\RefreshMoodleTasks;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->loadCount('referrals');

        // Store version and bump `updated_at` time
        $request->user()->update(['version' => $this->getVersion()]);
        // $request->user()->touch();

        return new UserResource($user);
    }

    private function getVersion(): ?string
    {
        $regex = '/^75grand\/.+ (\d+\.\d+\.\d+)$/';
        $userAgent = request()->userAgent();
        return Str::match($regex, $userAgent) ?: request()->user()->version;
    }

    public function update(Request $request)
    {
        $years = range(date('Y'), date('Y') + 4);
        $user = $request->user();

        $data = $request->validate([
            'expo_token' => ['nullable', 'string'],
            'class_year' => ['nullable', 'integer', Rule::in($years)],
            'position' => ['nullable', 'string', 'in:student,professor,staff'],
            'phone' => ['nullable', 'string', 'digits:10'],
            'moodle_token' => ['nullable', 'required_with:moodle_user_id', 'string'],
            'moodle_user_id' => ['nullable', 'required_with:moodle_token', 'integer']
        ]);

        $user->update($data);

        if($user->wasChanged(['moodle_token', 'moodle_user_id']) && $user->moodle_url !== null) {
            RefreshMoodleTasks::dispatchSync($user);
        }

        $user = $user->loadCount('referrals');
        return new UserResource($user);
    }
}
