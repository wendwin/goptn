<?php

use Illuminate\Support\Facades\Artisan;
use App\Jobs\SendAdmissionReminderJob;
use Illuminate\Console\Scheduling\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| Di sini kamu dapat mendefinisikan perintah Artisan kustom dan menjadwalkan job.
|
*/

// Perintah Artisan kustom untuk mengirim admission reminder
Artisan::command('reminder:admission', function () {
    SendAdmissionReminderJob::dispatch();
    $this->info('Admission reminder job dispatched.');
})->purpose('Send admission reminder emails to students');

// Menjadwalkan perintah setiap hari jam 08:00
app()->booted(function () {
    $schedule = app(Schedule::class);

    // Testing cepat
    // $schedule->command('reminder:admission')->everyMinute();

    // Untuk production: menjalankan job setiap hari jam 05:25
    $schedule->command('reminder:admission')->dailyAt('06:30')
             ->timezone('Asia/Jakarta');
});

