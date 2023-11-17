<?php

namespace Payment;

use App\Events\OrderPaid;
use App\Models\Order;
use App\Models\OrderPerformer;
use App\Models\User;
use Tests\Feature\Trait\PrepareForTestWithSeedTrait;
use Tests\TestCase;

class PaymentTest extends TestCase
{

    use PrepareForTestWithSeedTrait;

    /**@test */
    public function test_a_order_can_be_paid(): void
    {
        /** @var User $user */
        $user = User::query()->withWhereHas('orders')->first();
        $order = $user->orders->first();
        $route = route('back.api.orders.pay', $order->id);
        $jwt = $this->getJwt($user);

        $this->post($route)->assertUnauthorized();

        $this->withoutExceptionHandling();

        $this->withHeader('Authorization', $jwt)
            ->expectsEvents(OrderPaid::class)
            ->post($route)
            ->assertOk()
            ->assertContent(route('client.orders.index', '', false));
        $this->assertNotNull($order->fresh()->pay_id);
        $this->assertFalse($order->orderPerformers()->whereNot('status', OrderPerformer::STATUS_WAIT_DELIVERY)->exists());
    }

    /**@test */
    public function test_a_order_can_be_refund(): void
    {
        $order = Order::query()->first();
        /** @var User $user */
        $user = $order->user()->first();
        $route = route('back.api.orders.refund', $order->id);
        $jwt = $this->getJwt($user);

        $this->post($route)->assertUnauthorized();

        $this->withHeader('Authorization', $jwt)
            ->post($route)
            ->assertOk();
        $this->assertNull($order->fresh()->refund_id);

        $this->withoutExceptionHandling();

        $order->orderPerformers()->take(1)->delete();
        $order->orderPerformers()->update(['status' => OrderPerformer::STATUS_RECEIVED]);
        $order->update(['pay_id' => uniqid('', true), 'status' => Order::STATUS_COMPLETED]);

        $this->withHeader('Authorization', $jwt)
            ->post($route)
            ->assertOk();
        $this->assertNotNull($order->fresh()->refund_id);
    }

    /**@test */
    public function test_a_order_performer_can_be_payout(): void
    {
        $orderPerformer = OrderPerformer::query()->first();
        /** @var User $user */
        $user = $orderPerformer->saler()->first();
        $route = route('admin.orders.payout', $orderPerformer->id);

        $this->post($route)->assertNotFound();

        session(['user.role' => $user->role]);
        $this->actingAs($user)->post($route)->assertForbidden();

        $this->withoutExceptionHandling();

        $orderPerformer->update(['status' => OrderPerformer::STATUS_RECEIVED, 'payout_id' => null]);
        $orderPerformer->order()->update(['status' => Order::STATUS_COMPLETED]);
        $this->actingAs($user)
            ->post($route)
            ->assertRedirect();
        $this->assertNotNull($orderPerformer->refresh()->payout_id);
        $this->assertTrue($orderPerformer->status == OrderPerformer::STATUS_PAYOUT);
        $orderPerformer->update(['status' => OrderPerformer::STATUS_RECEIVED, 'payout_id' => null]);
    }
}
