<?php

namespace App\Components\Transport\Consumer\Payment;

use App\Dto\PaymentDto;

class HttpPaymentTransport extends AbstractPaymentTransport
{

    public function payout(PaymentDto $paymentDto): void
    {
        $this->getHttpTransport()
            ->setQuery((array)$paymentDto)
            ->setHeader('requester-id', config('consumers.payment.requester_id'))
            ->setMethod('POST')
            ->setUri(config('consumers.payment.options.http.urls.payment'))
            ->publish();
    }

    public function refund(PaymentDto $paymentDto): void
    {
        $this->getHttpTransport()
            ->setQuery((array)$paymentDto)
            ->setHeader('requester-id', config('consumers.payment.requester_id'))
            ->setMethod('POST')
            ->setUri(config('consumers.payment.options.http.urls.payment'))
            ->publish();
    }
}
