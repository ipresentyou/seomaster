<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;

class SendAdminNewUserNotification
{
    public function handle(Registered $event): void
    {
        $user = $event->user;

        Mail::raw(
            "Neuer User registriert:\n\nName: {$user->name}\nE-Mail: {$user->email}\nZeit: " . now()->format('d.m.Y H:i') . "\n\nDirekt zum Admin: " . url('/admin'),
            function ($message) use ($user) {
                $message->to('mail@kreativ.team')
                        ->subject("🆕 Neuer User: {$user->name}");
            }
        );
    }
}
