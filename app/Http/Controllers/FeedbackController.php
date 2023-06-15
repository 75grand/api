<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FeedbackController extends Controller
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

        webhook_alert('New feedback', [
            'Email' => $data['email'] ?? '(no email provided)',
            'Message' => $data['message']
        ]);
    }
}
