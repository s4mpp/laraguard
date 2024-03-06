<?php

namespace S4mpp\Laraguard\Notifications;

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
            ->subject(__('laraguard::password.mail.subject'))  // @phpstan-ignore-line
            ->line(__('laraguard::password.mail.text')) 
            ->action(__('laraguard::password.mail.action'), $this->url)  // @phpstan-ignore-line
            ->line(__('laraguard::password.mail.expiration', [
                'count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire'),
            ]))
            ->line(__('laraguard::password.mail.notice'));
    }
}
