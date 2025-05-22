<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DailyTimeExceededNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $hoursLogged;
    protected $date;

    /**
     * Create a new notification instance.
     */
    public function __construct($hoursLogged, $date)
    {
        $this->hoursLogged = $hoursLogged;
        $this->date = $date;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Daily Work Time Exceeds 8 Hours')
            ->greeting('Hello ' . $notifiable->name)
            ->line('You have logged ' . number_format($this->hoursLogged, 2) . ' hours on ' . $this->date . '.')
            ->line('This exceeds the recommended daily work limit of 8 hours.')
            ->line('Remember to take breaks and maintain a healthy work-life balance.')
            ->action('View Your Time Logs', url('/'))
            ->line('Thank you for using our Freelance Time Tracker!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'hours_logged' => $this->hoursLogged,
            'date' => $this->date,
            'message' => 'Daily work time exceeds 8 hours'
        ];
    }
}
