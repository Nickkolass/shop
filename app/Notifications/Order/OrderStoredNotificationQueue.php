<?php

namespace App\Notifications\Order;

use App\Events\Order\OrderStored;
use App\Mail\MailOrderStoredCanceled;
use Illuminate\Support\Facades\Mail;

class OrderStoredNotificationQueue
{
    public function handle(OrderStored $event): void
    {
        $text = "Оформлен новый заказ в Lumos № {$event->order->id} от {$event->order->created_at} на сумму {$event->order->total_price}";
        $mail = new MailOrderStoredCanceled($text, $event->order->productTypes);
        Mail::to(session('user.email'))->send($mail);
    }
}
