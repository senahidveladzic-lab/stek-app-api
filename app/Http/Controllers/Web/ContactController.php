<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('contact');
    }

    public function send(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        // TODO: dispatch a mail job once mail is configured

        return redirect()->route('contact')->with('success', true);
    }
}
