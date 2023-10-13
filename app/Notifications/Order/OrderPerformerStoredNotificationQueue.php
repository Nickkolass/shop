<?php

namespace App\Notifications\Order;

use App\Events\Order\OrderPerformerStored;
use App\Mail\MailOrderStoredCanceled;
use Illuminate\Support\Facades\Mail;

class OrderPerformerStoredNotificationQueue
{

    public function handle(OrderPerformerStored $event): void
    {
        $text = "У вас новый заказ в Lumos от {$event->order->created_at} на сумму {$event->order->total_price}.
            Доставьте его заказчику до  {$event->order->dispatch_time}  по следующим реквизитам: {$event->order->delivery}";
        $mail = new MailOrderStoredCanceled($text, $event->order->productTypes);
        Mail::to($event->order->saler->email)->send($mail);
    }
}
