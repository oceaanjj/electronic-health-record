<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $token;
    public $source;

    public function __construct($token, $source = 'web')
    {
        $this->token = $token;
        $this->source = $source;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Safely generate the reset URL using the notifiable user's email.
        // This prevents issues when the email is sent via a background queue.
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
            'source' => $this->source,
        ], false));

        return (new MailMessage)
            ->subject('Reset Your EHR Password')
            ->view('emails.password-reset', [
                'user' => $notifiable,
                'url' => $url,
            ]);
    }
}
