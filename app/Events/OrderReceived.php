<?php

namespace App\Events;

use App\Models\Order;
use App\Models\OrderPerformer;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderReceived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Order $order)
    {
        $this->order
            ->load([
                'user:id,email',
                'orderPerformers' => function (Builder $q) {
                    /** @phpstan-ignore-next-line */
                    $q->with('saler:id,email,card')
                        ->withTrashed();
                }])
            ->orderPerformers
            ->whereNull('deleted_at')
            ->each(function (OrderPerformer $orderPerformer) {
                event(new OrderPerformerReceived($orderPerformer));
            });
    }
}
