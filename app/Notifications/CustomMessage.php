<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CustomMessage extends Notification
{
    use Queueable;

    private $message,$uuid;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message,$uuid)
    {
        $this->message = $message;
        $this->uuid = $uuid;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
            'uuid' => $this->uuid
        ];
    }
}
