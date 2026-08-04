<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('contact.index', [
            'formToken' => Crypt::encryptString((string) now()->timestamp),
        ]);
    }

    public function submit(Request $request)
    {
        // Honeypot: real users never fill this hidden field, bots usually do.
        if (filled($request->input('website'))) {
            return redirect()->route('contact.index')
                ->with('success', '✅ Deine Nachricht wurde erfolgreich gesendet. Wir melden uns bald bei dir!');
        }

        // Minimum fill time: bots submit near-instantly after loading the page.
        $renderedAt = rescue(fn () => (int) Crypt::decryptString((string) $request->input('form_token')), 0, false);
        if (now()->timestamp - $renderedAt < 3) {
            return redirect()->route('contact.index')
                ->with('success', '✅ Deine Nachricht wurde erfolgreich gesendet. Wir melden uns bald bei dir!');
        }

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
