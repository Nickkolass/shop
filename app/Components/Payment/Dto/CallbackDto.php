<?php

namespace App\Components\Payment\Dto;

use App\Models\Order;
use App\Models\OrderPerformer;

class CallbackDto
{
    public function __construct(
        public readonly string               $id,
        public readonly string               $event,
        public readonly string               $status,
        public readonly Order|OrderPerformer $order,
    )
    {
    }
}
