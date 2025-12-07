<?php

// namespace App\Jobs;

// use App\Events\AdmissionReminderEvent;
// use App\Models\AdmissionItem;
// use App\Models\User;
// use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Foundation\Bus\Dispatchable;
// use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Queue\SerializesModels;
// use Illuminate\Support\Facades\Log;

// class SendAdmissionReminderJob implements ShouldQueue
// {
//     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//     public function handle()
//     {
//         Log::info("SendAdmissionReminderJob dijalankan pada " . now());

//         $dates = [ now()->toDateString() ];
//         $items = AdmissionItem::where('name', 'Registrasi Akun SNPMB Siswa')
//             ->whereIn('start_date', $dates)
//             ->get();

//         Log::info("Jumlah item ditemukan: " . $items->count());
//         $students = User::where('role', 'student')
//                         ->where('wants_notification', true)
//                         ->get();

//         Log::info("Jumlah siswa yang akan dikirimi notifikasi: " . $students->count());

//         foreach ($items as $item) {
//             foreach ($students as $student) {
//                 Log::info("Memanggil event untuk student: {$student->email}, item: {$item->name}");
//                 event(new AdmissionReminderEvent($student, $item));
//             }
//         }

//         Log::info("SendAdmissionReminderJob selesai dijalankan pada " . now());
//     }
// }

namespace App\Jobs;

use App\Events\AdmissionReminderEvent;
use App\Models\AdmissionItem;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendAdmissionReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        Log::info("SendAdmissionReminderJob dijalankan pada " . now());

        $today = Carbon::today('Asia/Jakarta');
        $reminderDays = [7, 1];

        $students = User::where('role', 'student')
                        ->where('wants_notification', true)
                        ->get();

        Log::info("Jumlah siswa yang akan dikirimi notifikasi: " . $students->count());

        // 1. SNBP
        $snbpItemNames = [
            'Registrasi Akun SNPMB Siswa',
            'Pendaftaran SNBP',
            'Pengumuman Hasil SNBP'
        ];

        foreach ($reminderDays as $daysBefore) {
            $targetDate = $today->copy()->subDays($daysBefore); // H sebelum

            // Ambil item SNBP yang relevan
            $snbpItems = AdmissionItem::whereIn('name', $snbpItemNames)
                            ->where('category', 'snbp')
                            ->whereDate('start_date', $targetDate->toDateString())
                            ->get();

            Log::info("SNBP - H-{$daysBefore}, targetDate: {$targetDate->toDateString()}, items: " . $snbpItems->pluck('name')->join(', '));

            foreach ($snbpItems as $item) {
                foreach ($students as $student) {
                    Log::info("Event SNBP: student {$student->email}, item {$item->name}");
                    event(new AdmissionReminderEvent($student, $item));
                }
            }
        }

        // 2. SNBT dan Mandiri
        $otherCategories = ['snbt', 'mandiri'];

        foreach ($otherCategories as $category) {
            $items = AdmissionItem::where('category', $category)->get();

            foreach ($items as $item) {
                foreach ($reminderDays as $daysBefore) {
                    $targetDate = $today->copy()->subDays($daysBefore); // H sebelum
                    if ($item->start_date == $targetDate->toDateString()) {
                        foreach ($students as $student) {
                            Log::info("Event {$category}: student {$student->email}, item {$item->name}, start_date {$item->start_date}");
                            event(new AdmissionReminderEvent($student, $item));
                        }
                    }
                }
            }
        }

        Log::info("SendAdmissionReminderJob selesai dijalankan pada " . now());
    }
}
