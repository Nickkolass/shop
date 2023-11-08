<?php

namespace App\Events\Order;

use App\Models\OrderPerformer;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPerformerReceived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly OrderPerformer $order)
    {
    }

}
