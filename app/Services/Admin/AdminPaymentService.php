<?php

namespace App\Services\Admin;

use App\Components\HttpClient\HttpClientInterface;
use App\Models\User;

class AdminPaymentService
{

    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    public function getWidget(User $user): string
    {
        $data = [
            '_token' => csrf_token(),
            'route' => route('users.card.update', $user->id),
        ];
        return $this->httpClient
            ->setJwt()
            ->setQuery($data)
            ->setUri(route('payment.card.edit', '', false))
            ->setMethod('POST')
            ->send()
            ->getBody()
            ->getContents();
    }

    /**
     * @param array<mixed> $data
     * @return string
     */
    public function cardValidate(array $data): string
    {
        return $this->httpClient
            ->setJwt()
            ->setQuery($data)
            ->setUri(route('payment.card.validate', '', false))
            ->setMethod('POST')
            ->send()
            ->getBody()
            ->getContents();
    }

    public function cardUpdate(User $user, string $card): void
    {
        $user->update(['card' => json_decode($card, true)['data']]);
    }

    /**
     * @param array{order_id: int, price: int, payout_token: string} $data
     * @return void
     */
    public function payout(array $data): void
    {
        $this->httpClient
            ->setJwt()
            ->setQuery($data)
            ->setUri(route('payment.payout', '', false))
            ->setMethod('POST')
            ->send();
    }
}
