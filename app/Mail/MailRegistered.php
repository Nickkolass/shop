<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailRegistered extends Mailable implements ShouldQueue
{
    use SerializesModels, Queueable;

    public ?string $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(?string $password = null)
    {
        $this->password = $password;
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Mail Registered',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content()
    {
        return new Content(
            view: 'mail.registered',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<empty>
     */
    public function attachments()
    {
        return [];
    }
}
