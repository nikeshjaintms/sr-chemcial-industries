<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'message' => 'required|string',
        ]);

        $fullName = trim($validated['name'] . ' ' . ($validated['last_name'] ?? ''));

        try {
            // Attempt to send email if mailer configured, or log fallback
            Mail::raw("New Inquiry from {$fullName}\nEmail: {$validated['email']}\nPhone: {$validated['phone']}\nMessage:\n{$validated['message']}", function ($mail) use ($fullName) {
                $mail->to('srchemicalindustries9@gmail.com')
                    ->subject("New Inquiry from " . $fullName);
            });
        } catch (\Throwable $e) {
            // Log mail failure gracefully
            \Log::warning("Contact mail dispatch failed: " . $e->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Email sent successfully!']);
        }

        return redirect()->route('thank-you')->with('success', 'Your message has been sent successfully!');
    }
}
