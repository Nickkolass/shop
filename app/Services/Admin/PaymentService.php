<?php

namespace App\Services\Admin;

use App\Components\HttpClient\HttpClientInterface;
use App\Models\User;

class PaymentService
{

    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    public function getWidget(User $user): string
    {
        $data = [
            '_token' => csrf_token(),
            'return_url' => route('admin.users.card.update', $user->id),
        ];
        return $this->httpClient
            ->setQuery($data)
            ->setUri('host.docker.internal:8877/api/payment/card/widget')
            ->setMethod('POST')
            ->send()
            ->getBody()
            ->getContents();
    }

    /**
     * @param array<mixed> $data
     * @return bool
     */
    public function cardValidate(array $data): bool
    {
        $code = $this->httpClient
            ->setQuery($data)
            ->setUri('host.docker.internal:8877/api/payment/card/validate')
            ->setMethod('POST')
            ->send()
            ->getStatusCode();
        return $code == 200;
    }

    public function cardUpdate(User $user, string $card): void
    {
        $user->update(['card' => json_decode($card, true)]);
    }

    /**
     * @param array{order_id: int, price: int, payout_token: string} $data
     * @return void
     */
    public function payout(array $data): void
    {
        $this->httpClient
            ->setQuery($data)
            ->setUri('host.docker.internal:8877/api/payment/payout')
            ->setMethod('POST')
            ->send();
    }
}
