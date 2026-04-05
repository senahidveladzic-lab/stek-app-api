<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\ContactFormReceived;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('contact');
    }

    public function send(Request $request): RedirectResponse
    {
        // Honeypot: bots fill hidden fields humans never see
        if ($request->filled('_hp')) {
            return redirect()->route('contact')->with('success', true);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        Mail::to(config('mail.contact_recipient'))
            ->send(new ContactFormReceived(
                senderName: $validated['name'],
                senderEmail: $validated['email'],
                contactSubject: $validated['subject'],
                messageBody: $validated['message'],
            ));

        return redirect()->route('contact')->with('success', true);
    }
}
