<?php

namespace App\Listeners\Payment;

use App\Components\Yookassa\YooKassaClient;
use App\Events\Order\Payment;
use App\Services\Client\API\Order\OrderDBService;
use DateTime;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Throwable;
use YooKassa\Model\Payment\PaymentStatus;

class PaymentListener implements ShouldQueue
{
    use InteractsWithQueue;

    public bool $afterCommit = true;

    public function __construct(private readonly YooKassaClient $yooKassa, private readonly OrderDBService $DBService)
    {
    }

    public function handle(Payment $event): void
    {
        sleep(10);
        $payment = $this->yooKassa->getPaymentInfo($event->payment_id);
        if ($payment->status == PaymentStatus::PENDING) $this->fail();
        $this->DBService->completeStore($event->order, $payment->id);
    }

    public function retryUntil(): DateTime
    {
        return now()->addMinutes(5);
    }

    public function failed(Payment $event, Throwable $exception): void
    {
        if (!isset($event->order->refresh()->payment_status)) {
            $this->DBService->delete($event->order, true);
        }
    }
}
