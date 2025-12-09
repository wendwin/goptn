<?php

namespace App\Listeners;

use App\Events\AdmissionReminderEvent;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendAdmissionReminder
{
    public function handle(AdmissionReminderEvent $event)
    {
        // Log awal, sebelum kirim email
        Log::info("Listener dipanggil untuk student: {$event->student->email}, item: {$event->item->name}");

        // Kirim email
        Mail::to($event->student->email)->send(
            new \App\Mail\AdmissionReminderMail($event->student, $event->item)
        );

        // Log setelah email dikirim
        Log::info("Email berhasil dikirim ke: {$event->student->email}, item: {$event->item->name}");
    }
}
