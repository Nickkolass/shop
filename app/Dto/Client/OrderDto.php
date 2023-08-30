<?php

namespace App\Dto\Client;

use Carbon\Carbon;

class OrderDto
{
    public function __construct(
        public readonly int $user_id,
        public readonly int $saler_id,
        public readonly int $order_id,
        public readonly Carbon $dispatch_time,
        public readonly string $status,
        public readonly array $productTypes,
        public readonly string $delivery,
        public readonly int $total_price,
    )
    {
    }
}
