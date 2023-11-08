<?php

namespace App\Events\Order;

use App\Models\Order;
use App\Models\OrderPerformer;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCanceled implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param Order $order
     * @param array<int> $orderPerformer_deleted_ids
     */
    public function __construct(
        public Order          $order,
        public readonly array $orderPerformer_deleted_ids,
    )
    {
        $this->order
            ->load(['orderPerformers' => function (Builder $q) {
                /** @phpstan-ignore-next-line */
                $q->select('id', 'order_id', 'saler_id', 'productTypes', 'total_price', 'created_at')
                    ->with('saler:id,email')
                    ->whereIn('id', $this->orderPerformer_deleted_ids)
                    ->onlyTrashed();
            }])
            ->orderPerformers
            ->each(function (OrderPerformer $orderPerformer) {
                event(new OrderPerformerCanceled($orderPerformer));
            });
    }
}
