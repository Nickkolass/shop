<?php

namespace App\Events;

use App\Models\OrderPerformer;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPerformerCanceled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly OrderPerformer $order, public readonly bool $canceler_is_client = true)
    {
    }
}
