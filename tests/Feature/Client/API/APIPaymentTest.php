<?php

namespace Client\API;

use App\Components\Transport\Consumer\Payment\PaymentTransportInterface;
use App\Models\Order;
use App\Models\User;
use Mockery\MockInterface;
use Tests\Feature\Trait\PrepareForTestWithSeedTrait;
use Tests\TestCase;

class APIPaymentTest extends TestCase
{

    use PrepareForTestWithSeedTrait;

    /**@test */
    public function test_a_order_can_be_paid_api(): void
    {
        $order = Order::query()
            ->with('orderPerformers:id,order_id,status')
            ->where('status', Order::STATUS_WAIT_PAYMENT)
            ->first();
        $data = ['return_url' => fake()->url()];
        $route = route('back.api.orders.pay', $order->id);
        $user = User::query()->first();
        $jwt = $this->getJwt($user);

        $this->withoutExceptionHandling();

        $this->partialMock(PaymentTransportInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('pay')->once();
        });

        $this->withToken($jwt)
            ->post($route, $data)
            ->assertOk();
    }

    /**@test */
    public function test_a_order_can_be_refund_api(): void
    {
        $order = Order::query()->first();
        $order->update(['pay_id' => uniqid(), 'status' => Order::STATUS_COMPLETED]);
        $order->orderPerformers()->delete();

        $route = route('back.api.orders.refund', $order->id);
        $user = $order->user()->first();
        $jwt = $this->getJwt($user);

        $this->withoutExceptionHandling();

        $this->partialMock(PaymentTransportInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('refund')->once();
        });

        $this->withToken($jwt)
            ->post($route)
            ->assertOk();
    }
}
