<?php

namespace App\Components\Yookassa;

use App\Models\Order;
use YooKassa\Client;
use YooKassa\Model\Payment\PaymentInterface;

class YooKassaClient
{
    public Client $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuth(...config('yookassa'));
    }

    public static function make(): self
    {
        return new self;
    }

    public function getPaymentInfo(string $paymentId): ?PaymentInterface
    {
        return $this->client->getPaymentInfo($paymentId);
    }

    /**
     * @param Order $order
     * @return array{payment_id:string,payment_url:string}
     */
    public function payment(Order $order): array
    {
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
        ], uniqid('', true));

        return [
            'payment_id' => $payment->id,
            'payment_url' => $payment->getConfirmation()->getConfirmationUrl(),
        ];
    }

    public function createRefund(string $paymentId, int $total_price): string
    {
        return $this->client->createRefund([
            'payment_id' => $paymentId,
            'amount' => [
                'value' => $total_price,
                'currency' => 'RUB',
            ]], uniqid('', true))->id;
    }
}

