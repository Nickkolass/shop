<?php

namespace App\Jobs\Client\Order;

use App\Models\Order;
use App\Services\Client\API\Order\OrderDBService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OrderStoredJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly Order $order)
    {
    }

    public function handle(): void
    {
        if ($this->order->status == Order::STATUS_WAIT_PAYMENT) {
            app(OrderDBService::class)->delete($this->order, true);
        }
    }
}

