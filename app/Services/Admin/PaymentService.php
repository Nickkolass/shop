<?php

namespace App\Services\Admin;

use App\Components\Transport\Consumer\Payment\PaymentTransportInterface;
use App\Dto\PaymentDto;
use App\Models\User;
use GuzzleHttp\Exception\ClientException;

class PaymentService
{

    public function __construct(private readonly PaymentTransportInterface $transport)
    {
    }

    public function getCardWidget(User $user): string
    {
        $data = [
            '_token' => csrf_token(),
            'return_url' => route('admin.users.card.update', $user->id),
        ];
        return $this->transport->getCardWidget($data);
    }

    public function cardUpdate(User $user, string $data): mixed
    {
        $card = json_decode($data, true);
        try {
            $this->transport->cardValidate($card);
            return $user->update(['card' => $card]);
        } catch (ClientException $exception) {
            $errors = $exception->getResponse()->getBody()->getContents();
            return json_decode($errors, true);
        }
    }

    public function payout(PaymentDto $paymentDto): void
    {
        $this->transport->payout($paymentDto);
    }
}
