<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\AdmissionReminderEvent;
use App\Listeners\SendAdmissionReminder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Daftarkan listener untuk AdmissionReminderEvent
        Event::listen(
            AdmissionReminderEvent::class,
            [SendAdmissionReminder::class, 'handle']
        );
    }
}
