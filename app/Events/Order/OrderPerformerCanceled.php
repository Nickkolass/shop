<?php

namespace App\Events\Order;

use App\Models\OrderPerformer;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPerformerCanceled implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly OrderPerformer $order, public readonly bool $canceler_is_client = true)
    {
    }
}
