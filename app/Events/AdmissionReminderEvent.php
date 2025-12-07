<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class AdmissionReminderEvent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

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

    public function broadcastOn()
    {
        return ['admission-reminder-'.$this->userId];
    }

    public function broadcastAs()
    {
        return 'AdmissionReminder';
    }
}

