<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
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
        $years = array_map(fn($n) => $n + date('Y'), range(0, 4));

        $data = $request->validate([
            'expo_token' => ['nullable', 'string'],
            'class_year' => ['nullable', 'integer', Rule::in($years)],
            'position' => ['nullable', 'string', 'in:student,professor,staff'],
            'phone' => ['nullable', 'string', 'digits:10'],
            'moodle_token' => ['nullable', 'string'],
            'moodle_user_id' => ['nullable', 'integer']
        ]);

        $request->user()->update($data);

        $user = $request->user()->loadCount('referrals');
        return new UserResource($user);
    }
}
