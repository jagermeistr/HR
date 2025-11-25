<?php

namespace App\Notifications;

use App\Models\Feedback;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewFeedbackNotification extends Notification
{
    use Queueable;

    public function __construct(public Feedback $feedback)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $feedbackType = ucfirst($this->feedback->type);
        $senderName = $this->feedback->is_anonymous ? 'Anonymous' : $this->feedback->sender->name;

        // Force use of current app URL
        $feedbackUrl = url('/feedback/' . $this->feedback->id);

        return (new MailMessage)
            ->subject("📝 {$feedbackType} Feedback from {$senderName}")
            ->greeting("Hello {$this->feedback->employee->name}!")
            ->line("You have received {$this->feedback->type} feedback from {$senderName}.")
            ->line('**Feedback Message:**')
            ->line($this->feedback->message)
            ->action('View Feedback', $feedbackUrl)
            ->line('Thank you for using our HR system!');
    }
}