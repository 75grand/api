<?php

namespace App\Http\Controllers;

class UserController extends Controller
{
    public function patch()
    {
        if(!auth()->check()) abort(401);

        $data = request()->validate([
            'class_year' => [
                'integer',
                'min:' . date('Y'), // Current senior
                'max:' . date('Y') + 4 // Incoming first-year
            ],
            'moodle_token' => 'string|nullable',
            'moodle_user_id' => 'integer|nullable'
        ]);

        auth()->user()->update($data);
    }
}
