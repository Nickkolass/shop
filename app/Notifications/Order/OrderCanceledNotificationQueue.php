<?php

namespace App\Notifications\Order;

use App\Events\Order\OrderCanceled;
use App\Mail\MailOrderStoredCanceled;
use Illuminate\Support\Facades\Mail;

class OrderCanceledNotificationQueue
{

    public function handle(OrderCanceled $event): void
    {
        $text = "Ваш заказ в Lumos № {$event->order->id} от {$event->order->created_at} на сумму {$event->order->total_price} отменен.
            Мы уже отправили денежные средства по реквизитам, с которых произведена оплата.";
        $mail = new MailOrderStoredCanceled($text, $event->order->productTypes);
        Mail::to(session('user.email'))->send($mail);
    }
}
