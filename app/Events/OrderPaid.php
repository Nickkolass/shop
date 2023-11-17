<?php

namespace App\Events;

use App\Models\Order;
use App\Models\OrderPerformer;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPaid
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Order $order, public readonly string $pay_id)
    {
        $this->order
            ->load([
                'user:id,email',
                'orderPerformers' => function (Builder $q) {
                    $q->select('id', 'order_id', 'saler_id', 'productTypes', 'total_price', 'created_at')
                        ->with('saler:id,email');
                }])
            ->orderPerformers
            ->each(function (OrderPerformer $orderPerformer) {
                event(new OrderPerformerPaid($orderPerformer));
            });
    }

}
