<?php

namespace App\Notifications;

use App\Models\Feedback;
use App\Models\FeedbackResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewFeedbackResponseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Feedback $feedback, public FeedbackResponse $response)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('💬 New Response to Your Feedback')
            ->greeting("Hello {$this->feedback->sender->name}!")
            ->line("Your feedback to {$this->feedback->employee->name} has received a new response.")
            ->line('**Response:**')
            ->line($this->response->response)
            ->line('**Your Original Feedback:**')
            ->line(Str::limit($this->feedback->message, 200))
            ->action('View Conversation', route('feedback.show', $this->feedback))
            ->line('Thank you for using our feedback system!');
    }
}