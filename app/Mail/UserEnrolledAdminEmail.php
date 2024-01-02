<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserEnrolledAdminEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $training;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$training)
    {
        $this->user = $user;
        $this->training = $training;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Üye Online Eğitim İçeriğine Kaydoldu: ' . ($this->user->full_name ?? $this->user->name . ' ' . $this->user->surname),
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'mail.user-enrolled-admin-email',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
