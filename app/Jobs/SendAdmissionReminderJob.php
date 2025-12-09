<?php

namespace App\Jobs;

use App\Models\AdmissionItem;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\AdmissionReminderMail;

class SendAdmissionReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function handle()
{
    Log::info("SendAdmissionReminderJob dijalankan pada " . now());

    $today = Carbon::today('Asia/Jakarta');
    $reminderDays = [7, 1]; // H-7 & H-1

    // Ambil hanya user yang sudah set preferensi notifikasi dan tipe jalurnya
    $students = User::where('role', 'student')
                    ->where('wants_notification', true)
                    ->whereNotNull('notification_type')
                    ->get();

    Log::info("Total siswa dengan preferensi notifikasi: " . $students->count());

    // Ambil admission items + relasi jadwal admission
    $items = AdmissionItem::with('admission')->whereNotNull('start_date')->get();

    $totalItemsToSend = 0;
    $totalRemindersSent = 0;

    foreach ($items as $item) {

        if (!$item->admission) continue; // skip jika tidak ada relasi admission

        $startDate = Carbon::parse($item->start_date);
        if ($startDate->isPast()) continue;

        foreach ($reminderDays as $daysBefore) {
            $checkDate = $today->copy()->addDays($daysBefore);

            if (!$checkDate->isSameDay($startDate)) continue;

            Log::info("Reminder: {$item->name}, kategori: {$item->admission->category}, campus: {$item->admission->campus_id}");

            foreach ($students as $student) {

                // --- Filter logika jalur ---

                // Jika SNBP / SNBT
                if (in_array($student->notification_type, ['snbp', 'snbt'])) {
                    if ($student->notification_type !== $item->admission->category) {
                        continue; // Skip jika kategori tidak cocok
                    }
                }

                // Jika Mandiri, wajib cocok kampus
                if ($student->notification_type === 'mandiri') {
                    if ($student->campus_id !== $item->admission->campus_id) {
                        continue;
                    }
                }

                // --- Kirim email ---
                Mail::to($student->email)->send(new AdmissionReminderMail($student, $item));
                $totalRemindersSent++;

                Log::info("Email dikirim ke {$student->email} untuk item {$item->name}");
            }

            $totalItemsToSend++;
            break; // Hindari double reminder untuk hari sama
        }
    }

    Log::info("SUMMARY: Items processed: {$totalItemsToSend}, email terkirim: {$totalRemindersSent}");
}

}


   // public function handle()
    // {
    //     Log::info("SendAdmissionReminderJob dijalankan pada " . now());

    //     $today = Carbon::today('Asia/Jakarta');
    //     $reminderDays = [7, 1]; // H-7 dan H-1

    //     // Ambil semua siswa yang ingin notifikasi
    //     $students = User::where('role', 'student')
    //                     ->where('wants_notification', true)
    //                     ->get();

    //     Log::info("Jumlah siswa yang akan dikirimi notifikasi: " . $students->count());

    //     // Ambil semua item dengan start_date
    //     $items = AdmissionItem::whereNotNull('start_date')->get();

    //     $totalItemsToSend = 0;
    //     $totalRemindersSent = 0;

    //     foreach ($items as $item) {
    //         $startDate = Carbon::parse($item->start_date);

    //         if ($startDate->isPast()) continue;

    //         foreach ($reminderDays as $daysBefore) {
    //             $checkDate = $today->copy()->addDays($daysBefore);

    //             if ($checkDate->isSameDay($startDate)) {
    //                 Log::info("Kirim reminder H-{$daysBefore} untuk item: {$item->name}, start_date: {$item->start_date}");

    //                 foreach ($students as $student) {
    //                     Log::info("Kirim reminder ke: {$student->email}, item: {$item->name}, H-{$daysBefore}");
    //                     // Kirim email langsung di job
    //                     Mail::to($student->email)->send(new AdmissionReminderMail($student, $item));
    //                     $totalRemindersSent++;
    //                 }

    //                 $totalItemsToSend++;
    //                 // hanya kirim sekali per item per hari, keluar dari loop reminderDays
    //                 break;
    //             }
    //         }
    //     }

    //     Log::info("SendAdmissionReminderJob selesai dijalankan pada " . now());
    //     Log::info("Summary hari ini: total items untuk dikirim: {$totalItemsToSend}, total reminders dikirim: {$totalRemindersSent}");
    // }