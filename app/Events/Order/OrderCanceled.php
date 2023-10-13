<?php

namespace App\Events\Order;

use App\Models\Order;
use App\Models\OrderPerformer;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCanceled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Order $order)
    {
        $this->order
            ->orderPerformers()
            ->withTrashed()
            ->select('id', 'order_id', 'saler_id', 'productTypes', 'total_price', 'created_at')
            ->with('saler:id,email')
            ->get()
            ->each(function (OrderPerformer $orderPerformer) {
                event(new OrderPerformerCanceled($orderPerformer));
            });
    }
}
