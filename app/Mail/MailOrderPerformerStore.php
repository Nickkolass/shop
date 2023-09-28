<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class MailOrderPerformerStore extends Mailable
{
    use SerializesModels;


    /**
     * Create a new message instance.
     * @param Collection<string, int|string|Carbon> $order
     * @return void
     */
    public function __construct(public readonly Collection $order)
    {
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'New order',
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
            view: 'mail.orderPerformerStore',
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
