<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('contact.index');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:2000',
        ]);

        try {
            // Send email to admin
            Mail::to(config('mail.from.address'))->send(new ContactFormSubmitted($validated));
            
            return redirect()->route('contact.index')
                ->with('success', '✅ Deine Nachricht wurde erfolgreich gesendet. Wir melden uns bald bei dir!');
                
        } catch (\Throwable $e) {
            \Log::error('Contact form submission failed', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);
            
            return redirect()->route('contact.index')
                ->with('error', '❌ Leider konnte deine Nachricht nicht gesendet werden. Bitte versuche es später erneut.');
        }
    }
}
