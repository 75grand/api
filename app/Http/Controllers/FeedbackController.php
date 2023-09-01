<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
            try {
                Mail::raw(
                    $data['message'],
                    function($message) use ($data) {
                        $user = User::firstWhere('email', $data['email']);
                        $subject = '[75grand] Feedback from ' . ($user->name ?? 'User');
                        
                        $message->to('jpaulos@macalester.edu');
                        $message->subject($subject);
                        if($user) $message->replyTo($user->email);
                    }
                );
            } catch(Exception) {
                webhook_alert('New feedback', [
                    'Email' => $data['email'] ?? '(no email provided)',
                    'Message' => $data['message']
                ]);
            }
        })->afterResponse();
    }
}
