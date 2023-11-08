<?php

namespace App\Events\Order;

use App\Models\Order;
use App\Models\OrderPerformer;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderReceived implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Order $order)
    {
        $this->order
            ->load(['orderPerformers' => function (Builder $q) {
                /** @phpstan-ignore-next-line */
                $q->select('id', 'order_id', 'saler_id', 'productTypes', 'total_price', 'created_at', 'deleted_at')
                    ->with('saler:id,email,card')
                    ->withTrashed();
            }])
            ->orderPerformers
            ->whereNull('deleted_at')
            ->each(function (OrderPerformer $orderPerformer) {
                event(new OrderPerformerReceived($orderPerformer));
            });
    }
}
