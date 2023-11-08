<?php

namespace App\Notifications\Order;

use App\Events\Order\OrderCanceled;
use App\Events\Order\OrderPaid;
use App\Events\Order\OrderPerformerCanceled;
use App\Events\Order\OrderPerformerPaid;
use App\Events\Order\OrderPerformerReceived;
use App\Events\Order\OrderReceived;
use App\Mail\MailOrderStoredReceivedCanceled;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Mail;

class OrderNotificationSubscriber
{
    public function handleOrderPaid(OrderPaid $event): void
    {
        //заказчиком оплачен заказ, уведомление на email заказчика
        $text = "Оформлен новый заказ в Lumos № {$event->order->id} от {$event->order->created_at} на сумму {$event->order->total_price}";
        $mail = new MailOrderStoredReceivedCanceled($text, $event->order->productTypes);
        Mail::to(session('user.email'))->send($mail);
    }

    public function handleOrderPerformerPaid(OrderPerformerPaid $event): void
    {
        //заказчиком оплачен заказ, уведомление на email продавца
        $text = "У вас новый заказ в Lumos № {$event->order->id} от {$event->order->created_at} на сумму {$event->order->total_price}.
            Доставьте его заказчику до  {$event->order->dispatch_time}  по следующим реквизитам: {$event->order->delivery}";
        $mail = new MailOrderStoredReceivedCanceled($text, $event->order->productTypes);
        Mail::to($event->order->saler->email)->send($mail);
    }

    public function handleOrderReceived(OrderReceived $event): void
    {
        //заказчиком получен товар, уведомление на email заказчика
        $text = "Ваш заказ в Lumos № {$event->order->id} от {$event->order->created_at} выполнен";
        $productTypes = $event->order->orderPerformers->pluck('productTypes')->flatten(1);
        $mail = new MailOrderStoredReceivedCanceled($text, $productTypes);
        Mail::to(session('user.email'))->send($mail);
    }

    public function handleOrderPerformerReceived(OrderPerformerReceived $event): void
    {
        //заказчиком получен товар, уведомление на email продавца
        $text = "Отправленный Вами заказ в Lumos № {$event->order->id} от {$event->order->created_at} на сумму {$event->order->total_price}
            получен покупателем. Мы уже отправили денежные средства на Ваш счет";
        $mail = new MailOrderStoredReceivedCanceled($text, $event->order->productTypes);
        Mail::to($event->order->saler->email)->send($mail);
    }

    public function handleOrderCanceled(OrderCanceled $event): void
    {
        //отмена со стороны заказчика (также мб со стороны продавца если им отменен последний наряд), уведомление на email заказчика
        $text = "Ваш заказ в Lumos № {$event->order->id} от {$event->order->created_at} на сумму {$event->order->total_price} отменен.
            Мы уже отправили денежные средства по реквизитам, с которых произведена оплата.";
        $productTypes = $event->order->orderPerformers->pluck('productTypes')->flatten(1);
        $mail = new MailOrderStoredReceivedCanceled($text, $productTypes);
        Mail::to(session('user.email'))->send($mail);
    }

    public function handleOrderPerformerCanceled(OrderPerformerCanceled $event): void
    {
        if ($event->canceler_is_client) {
            //отмена со стороны заказчика (или продавца если отменен последний наряд), уведомление на email продавца
            $email = $event->order->saler->email;
            $text = "Доставка товаров в Lumos по заказу № {$event->order->id} от {$event->order->created_at}
            на сумму {$event->order->total_price} отменена.";
        } else {
            //отмена со стороны продавца, уведомление на email заказчика
            $email = $event->order->user()->pluck('email')[0];
            $text = "Часть Вашего заказа в Lumos № {$event->order->order_id} от {$event->order->created_at}
                на сумму {$event->order->total_price} отменена. Денежные средства будут возвращены
                после получения оставшейся части заказа либо при полной отменене заказа, по реквизитам, с которых произведена оплата.";
        }
        Mail::to($email)->send(new MailOrderStoredReceivedCanceled($text, $event->order->productTypes));
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @return array<string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            OrderPaid::class => 'handleOrderPaid',
            OrderPerformerPaid::class => 'handleOrderPerformerPaid',
            OrderReceived::class => 'handleOrderReceived',
            OrderPerformerReceived::class => 'handleOrderPerformerReceived',
            OrderCanceled::class => 'handleOrderCanceled',
            OrderPerformerCanceled::class => 'handleOrderPerformerCanceled',
        ];
    }
}
