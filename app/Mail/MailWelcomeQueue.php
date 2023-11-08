<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailWelcomeQueue extends Mailable implements ShouldQueue
{
    use SerializesModels, Queueable;

    public function __construct(public readonly ?string $password)
    {
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Mail welcome',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.registered',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<mixed>
     */
    public function attachments(): array
    {
        return [];
    }
}
