<?php

namespace App\Components\Payment\src\Clients;

use App\Components\Payment\src\Dto\CallbackDto;
use App\Models\Order;
use App\Models\OrderPerformer;
use YooKassa\Client;
use YooKassa\Model\Deal\DealInterface;
use YooKassa\Model\Deal\SettlementPayoutPaymentType;
use YooKassa\Model\Notification\NotificationCanceled;
use YooKassa\Model\Notification\NotificationEventType;
use YooKassa\Model\Notification\NotificationInterface;
use YooKassa\Model\Notification\NotificationPayoutCanceled;
use YooKassa\Model\Notification\NotificationPayoutSucceeded;
use YooKassa\Model\Notification\NotificationRefundSucceeded;
use YooKassa\Model\Notification\NotificationSucceeded;
use YooKassa\Model\Payment\PaymentInterface;
use YooKassa\Model\Payment\PaymentStatus;
use YooKassa\Model\Payout\PayoutInterface;
use YooKassa\Model\Receipt\ReceiptType;
use YooKassa\Model\Refund\RefundInterface;

class YooKassaClient extends AbstractPaymentClient
{

    const WIDGET_VIEW = 'payment::widget.yookassa';

    public function __construct(public readonly Client $client)
    {
    }

    public function setAuth(bool $is_payout = false): void
    {
        $credentials = config('payment.connections.yookassa.' . ($is_payout ? 'agent' : 'shop'));
        $this->client->setAuth(...$credentials);
    }

    /**
     * @param Order $order
     * @return string $pay_url
     */
    public function pay(Order $order): string
    {
        $this->setAuth();
        $payment = $this->client->createPayment([
            'amount' => [
                'value' => $order->total_price,
                'currency' => 'RUB',
            ],
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => config('app.url') . '/orders',
            ],
            'capture' => true,
            'description' => 'Заказ №' . $order->id,
            'metadata' => [
                'order_id' => $order->id,
            ],
        ], uniqid('', true));

        return $payment->confirmation->getConfirmationUrl();
    }

    public function payout(OrderPerformer $order): void
    {
        $this->setAuth(true);
        $this->client->createPayout([
            'amount' => [
                'value' => $order->total_price,
                'currency' => 'RUB',
            ],
            'payout_token' => $order->saler->card['payout_token'],
            'description' => 'Выплата по заказу №' . $order->id,
            'metadata' => [
                'order_id' => $order->id,
            ],
        ], uniqid('', true));
    }

    public function refund(Order $order, int $price): void
    {
        $this->setAuth();
        $this->client->createRefund([
            'payment_id' => $order->pay_id,
            'amount' => [
                'value' => $price,
                'currency' => 'RUB',
            ],
        ], uniqid('', true));
    }

    public function authorizeCallback(): void
    {
        if (app()->environment('production')) {
            $ips = ['185.71.76.0/27', '185.71.77.0/27', '77.75.153.0/25', '77.75.156.11', '77.75.156.35', '77.75.154.128/25', '2a02:5180::/32'];
            if (!in_array(request()->ip(), $ips)) abort(403);
        }
    }

    public function getWidget(): string
    {
        return self::WIDGET_VIEW;
    }

    public function callback(mixed $requestBody): CallbackDto
    {
        $notification = $this->getNotification($requestBody);
        $transaction = $notification->getObject();
        $event = $this->eventAdapter($notification->getEvent());
        $status = $this->statusAdapter($transaction->getStatus());
        $order = $this->getOrder($event, $transaction);

        return new CallbackDto(
            id: $transaction->id,
            event: $event,
            status: $status,
            order: $order,
        );
    }

    private function getNotification(mixed $requestBody): NotificationInterface
    {
        $notification = match ($requestBody['event']) {
//            NotificationEventType::PAYMENT_WAITING_FOR_CAPTURE => new NotificationWaitingForCapture($requestBody),
            NotificationEventType::PAYMENT_SUCCEEDED => new NotificationSucceeded($requestBody),
            NotificationEventType::PAYMENT_CANCELED => new NotificationCanceled($requestBody),
            NotificationEventType::REFUND_SUCCEEDED => new NotificationRefundSucceeded($requestBody),
            NotificationEventType::PAYOUT_SUCCEEDED => new NotificationPayoutSucceeded($requestBody),
            NotificationEventType::PAYOUT_CANCELED => new NotificationPayoutCanceled($requestBody),
//            NotificationEventType::DEAL_CLOSED => new NotificationDealClosed($requestBody),
            default => null,
        };
        if (is_null($notification)) abort(400);
        return $notification;
    }

    private function statusAdapter(?string $status): string
    {
        $status = match ($status) {
//            PaymentStatus::PENDING => self::TRANSACTION_STATUS_PENDING,
//            PaymentStatus::WAITING_FOR_CAPTURE => self::TRANSACTION_STATUS_WAITING,
            PaymentStatus::SUCCEEDED => self::TRANSACTION_STATUS_SUCCEEDED,
            PaymentStatus::CANCELED => self::TRANSACTION_STATUS_CANCELED,
            default => null,
        };
        if (is_null($status)) abort(400);
        return $status;
    }

    private function eventAdapter(?string $event): string
    {
        $event = match (explode('.', $event)[0]) {
            ReceiptType::PAYMENT => self::CALLBACK_EVENT_PAYMENT,
            ReceiptType::REFUND => self::CALLBACK_EVENT_REFUND,
            SettlementPayoutPaymentType::PAYOUT => self::CALLBACK_EVENT_PAYOUT,
//            'deal' => self::CALLBACK_EVENT_DEAL,
            default => null,
        };
        if (is_null($event)) abort(400);
        return $event;
    }

    private function getOrder(string $event, RefundInterface|PaymentInterface|PayoutInterface|DealInterface $transaction): Order|OrderPerformer
    {
        $id = $transaction->metadata->order_id ?? $transaction->payment_id ?? null;
        if (is_null($id)) abort(400);

        $order = match ($event) {
            self::CALLBACK_EVENT_PAYMENT => Order::query()->find($id),
            self::CALLBACK_EVENT_REFUND => Order::query()->withTrashed()->firstWhere('pay_id', $id),
            self::CALLBACK_EVENT_PAYOUT => OrderPerformer::query()->find($id),
//            'deal' => Order::query()->find($transaction->metadata->order_id),
            default => null,
        };
        if (is_null($order)) abort(400);
        return $order;
    }
}

