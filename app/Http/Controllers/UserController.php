<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(Request $request)
    {
        return $request->user();
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'expo_token' => ['nullable', 'string'],
            'macpass_number' => ['nullable', 'string', 'numeric', 'digits:9'],
            'class_year' => ['nullable', 'integer', 'digits:4'],
            'type' => ['nullable', 'string', 'in:student,professor,staff']
        ]);

        $request->user()->update($data);

        return $request->user();
    }
}
