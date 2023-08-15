<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class OrderStored
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Collection $orderPerformers;

    /**
     * Create a new event instance.
     *
     * @param  array $orderPerformers
     * @return void
     */
    public function __construct(Collection $orderPerformers)
    {
        $this->orderPerformers = $orderPerformers;
    }
}
