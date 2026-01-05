<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WalletUnder10EuroNotification extends Notification
{
    use Queueable;

    public int $balance;

    public function __construct(int $balance)
    {
        $this->balance = $balance;
    }


    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Votre portefeuille est sous les 10 euros')
            ->greeting('Bonjour')
            ->line('Votre solde est passé sous les 10 euros.')
            ->line('Cordialement!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
