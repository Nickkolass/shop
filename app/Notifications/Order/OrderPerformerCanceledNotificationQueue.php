<?php

namespace App\Notifications\Order;

use App\Events\Order\OrderPerformerCanceled;
use App\Mail\MailOrderStoredCanceled;
use Illuminate\Support\Facades\Mail;

class OrderPerformerCanceledNotificationQueue
{

    public function handle(OrderPerformerCanceled $event): void
    {
        if ($event->canceler_is_client) {
            //отмена со стороны заказчика, уведомление на email продавца
            $email = $event->order->saler->email;
            $text = "Заказчиком отменена доставка товаров в Lumos по заказу № {$event->order->id}
                от {$event->order->created_at} на сумму {$event->order->total_price}.";
        } else {
            //отмена со стороны продавца, уведомление на email заказчика
            $email = $event->order->user()->pluck('email')[0];
            $text = "Часть Вашего заказа в Lumos № {$event->order->order_id} от {$event->order->created_at}
                на сумму {$event->order->total_price} не сможет быть доставлена.
                Мы уже отправили денежные средства по реквизитам, с которых произведена оплата.";
        }
        Mail::to($email)->send(new MailOrderStoredCanceled($text, $event->order->productTypes));
    }
}
