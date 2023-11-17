<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class MailOrderStoredReceivedCanceled extends Mailable
{
    use SerializesModels;

    /**
     * @param string $text
     * @param array<mixed>|Collection<mixed> $productTypes
     */
    public function __construct(public readonly string $text, public readonly array|Collection $productTypes)
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
            subject: 'order',
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
            view: 'mail.order_stored_canceled',
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
