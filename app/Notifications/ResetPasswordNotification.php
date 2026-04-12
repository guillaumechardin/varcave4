<?php

namespace App\Notifications;

use App\Mail\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;


class ResetPasswordNotification extends Notification
{
    use Queueable;
    public $token = null;
    public $request = null;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
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
    public function toMail(object $user)
    {
        $url = url('/reset-password/'.$this->token.'?email='.urlencode($user->email));
        return new ResetPassword($url, $this->token, $user);

        /*Mail::to($user->email)
            ->send(new ResetPassword($url, $this->token, $user));
        */
        
        /*    $url = url('/reset-password/'.$this->token.'?email='.urlencode($notifiable->email));
        //$url = url('/reset-password/?email='.urlencode($user->email));

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Réinitialisation du mot de passe')
            ->view('emails.reset-password', [
                'url' => $url,
                'user' => $user,
            ]);
            */
    }
    

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
