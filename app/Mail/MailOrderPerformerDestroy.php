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

    public OrderPerformer $order;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(OrderPerformer $order)
    {
        $this->order = $order;
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope()
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
    public function content()
    {
        return new Content(
            view: 'mail.orderPerformerDelete',
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
