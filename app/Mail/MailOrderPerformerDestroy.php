<?php

namespace App\Mail;

use App\Models\OrderPerformer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailOrderPerformerDestroy extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public OrderPerformer $order)
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
            subject: 'delete orderPerformer',
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
            view: 'mail.orderPerformerDelete',
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
