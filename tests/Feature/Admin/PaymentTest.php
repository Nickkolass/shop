<?php

namespace Admin;

use App\Components\Transport\Consumer\Payment\PaymentTransportInterface;
use App\Models\Order;
use App\Models\OrderPerformer;
use App\Models\User;
use Mockery\MockInterface;
use Tests\Feature\Trait\PrepareForTestWithSeedTrait;
use Tests\TestCase;

class PaymentTest extends TestCase
{

    use PrepareForTestWithSeedTrait;

    /**@test */
    public function test_a_card_can_be_edited(): void
    {
        $user = User::query()->first();
        $route = route('admin.users.card.edit', $user->id);
        session(['user.role' => User::ROLE_SALER]);

        $this->withoutExceptionHandling();

        $this->partialMock(PaymentTransportInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('getCardWidget')->once();
        });

        $this->actingAs($user)
            ->get($route)
            ->assertViewIs('admin.user.card');
    }

    /**@test */
    public function test_a_card_can_be_updated(): void
    {
        $user = User::query()->first();
        $route = route('admin.users.card.update', $user->id);
        session(['user.role' => User::ROLE_SALER]);
        $data = [
            'payout_token' => uniqid(),
            'first6' => fake()->numerify('######'),
            'last4' => fake()->numerify('####'),
            'card_type' => fake()->creditCardType(),
            'issuer_country' => fake()->country(),
        ];

        $this->withoutExceptionHandling();

        $this->partialMock(PaymentTransportInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('cardValidate')->once();
        });

        $this->actingAs($user)
            ->patch($route, ['data' => json_encode($data)])
            ->assertRedirect();
        $this->assertEquals($user->refresh()->card, $data);
    }

    /**@test */
    public function test_a_order_can_be_payout(): void
    {
        $order = OrderPerformer::query()->first();
        $order->update(['status' => OrderPerformer::STATUS_RECEIVED]);
        $order->order()->update(['status' => Order::STATUS_COMPLETED]);
        $user = $order->saler()->first();
        session(['user.role' => User::ROLE_SALER]);
        $route = route('admin.orders.payout', $order->id);

        $this->withoutExceptionHandling();

        $this->partialMock(PaymentTransportInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('payout')->once();
        });

        $this->actingAs($user)
            ->post($route)
            ->assertRedirect();
    }
}
