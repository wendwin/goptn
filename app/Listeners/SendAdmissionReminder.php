<?php

namespace App\Listeners;

use App\Events\AdmissionReminderEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log; // <--- tambahkan ini

class SendAdmissionReminder implements ShouldQueue
{
    public function handle(AdmissionReminderEvent $event)
    {
        // Log::info("Listener dipanggil untuk student: {$event->student->email}");

        Mail::to($event->student->email)->send(
            new \App\Mail\AdmissionReminderMail($event->student, $event->item)
        );

        // Log::info("Email berhasil dikirim ke: {$event->student->email}");
    }
}
