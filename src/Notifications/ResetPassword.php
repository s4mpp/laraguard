<?php

namespace S4mpp\Laraguard\Notifications;

use S4mpp\Laraguard\Utils;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

final class ResetPassword extends Notification
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
            ->subject(Utils::translate('laraguard::password.mail.subject'))
            ->line(Utils::translate('laraguard::password.mail.text'))
            ->action(Utils::translate('laraguard::password.mail.action'), $this->url)
            ->line(Utils::translate('laraguard::password.mail.expiration', [
                'count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire'),
            ]))
            ->line(Utils::translate('laraguard::password.mail.notice'));
    }
}
