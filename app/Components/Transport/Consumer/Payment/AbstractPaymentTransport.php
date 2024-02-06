<?php

namespace App\Components\Transport\Consumer\Payment;

use App\Components\Transport\Consumer\AbstractConsumerTransport;
use App\Dto\PaymentDto;

abstract class AbstractPaymentTransport extends AbstractConsumerTransport implements PaymentTransportInterface
{

    /**
     * @param array<mixed> $data
     * @return string
     */
    public function getCardWidget(array $data): string
    {
        return $this->getHttpTransport()
            ->setQuery($data)
            ->setUri(config('consumers.payment.options.http.urls.get_widget'))
            ->setMethod('POST')
            ->send()
            ->getBody()
            ->getContents();
    }

    /**
     * @param array<mixed> $card
     * @return void
     */
    public function cardValidate(array $card): void
    {
        $this->getHttpTransport()
            ->setQuery($card)
            ->setUri(config('consumers.payment.options.http.urls.card_validate'))
            ->setMethod('POST')
            ->send();
    }

    public function pay(PaymentDto $paymentDto): string
    {
        return $this->getHttpTransport()
            ->setQuery((array)$paymentDto)
            ->setHeader('requester-id', config('consumers.payment.requester_id'))
            ->setMethod('POST')
            ->setUri(config('consumers.payment.options.http.urls.payment'))
            ->send()
            ->getBody()
            ->getContents();
    }
}
