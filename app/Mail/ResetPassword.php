<?php

namespace App\Mail;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $tokenExpires = null;
    /**
     * Create a new message instance.
     */
    public function __construct(
        public String $url,
        public String $token,
        public User $user)
    {
        $this->tokenExpires = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(Setting::get('smtp_sender')),
            to: $this->user->email,
            subject: __('reset-password.subject') .  ' ' . env('APP_NAME'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.reset-password',
            //text: 'mail.reset-password-txt',  //a simpler txt version
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
