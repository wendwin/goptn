<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class AdmissionReminderEvent
{
    use SerializesModels;

    public $student;
    public $item;
    public $message;
    public $userId;

    public function __construct($student, $item)
    {
        $this->student = $student;
        $this->item = $item;
        $this->userId = $student->id;
        $this->message = "Reminder: {$item->name} dimulai pada {$item->start_date}";
    }
}


