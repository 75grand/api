<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
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
