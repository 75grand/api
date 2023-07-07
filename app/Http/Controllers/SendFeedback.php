<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SendFeedback extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $data = request()->validate([
            'email' => 'nullable|email',
            'message' => 'required|string'
        ]);

        dispatch(function() use ($data) {
            webhook_alert('New feedback', [
                'Email' => $data['email'] ?? '(no email provided)',
                'Message' => $data['message']
            ]);
        })->afterResponse();
    }
}
