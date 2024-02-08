<?php

namespace S4mpp\Laraguard\Notifications;

use S4mpp\Laraguard\Utils;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $url)
    {
        //
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
            ->subject(Utils::translate('Reset Password Notification'))
            ->line(Utils::translate('You are receiving this email because we received a password reset request for your account.'))
            ->action(Utils::translate('Reset Password'), $this->url)
            ->line(Utils::translate('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
            ->line(Utils::translate('If you did not request a password reset, no further action is required.'));
    }
}
