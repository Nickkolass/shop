<?php

namespace Client\API;

use App\Events\OrderPaid;
use App\Models\Order;
use App\Models\OrderPerformer;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\Feature\Trait\PrepareForTestWithSeedTrait;
use Tests\TestCase;

class APIPaymentCallbackTest extends TestCase
{

    use PrepareForTestWithSeedTrait;

    /**@test */
    public function test_a_order_can_be_paid(): void
    {
        $route = route('back.api.payment.callback');
        $order = Order::query()
            ->with('orderPerformers:id,order_id,status')
            ->where('status', Order::STATUS_WAIT_PAYMENT)
            ->first();
        $data = [
            'id' => Str::random(20),
            'event' => 'pay',
            'order_id' => $order->id,
        ];
        $this->withoutExceptionHandling();

        $this->expectsEvents(OrderPaid::class)
            ->post($route, $data)
            ->assertOk();

        $order->refresh();
        $this->assertEquals($order->status, Order::STATUS_PAID);
        $this->assertNotNull($order->pay_id);
        $this->assertTrue($order->orderPerformers->where('status', '!=', OrderPerformer::STATUS_WAIT_DELIVERY)->isEmpty());
    }

    /**@test */
    public function test_a_order_can_be_refund(): void
    {
        $route = route('back.api.payment.callback');
        $order = Order::query()->first();
        $data = [
            'id' => Str::random(20),
            'event' => 'refund',
            'order_id' => $order->id,
        ];
        $this->withoutExceptionHandling();

        $this->post($route, $data)->assertOk();
        $order->refresh();
        $this->assertEquals($order->refund_id, $data['id']);
    }

    /**@test */
    public function test_a_order_performer_can_be_payout(): void
    {
        $route = route('back.api.payment.callback');
        $order = OrderPerformer::query()->first();
        $data = [
            'id' => Str::random(20),
            'event' => 'payout',
            'order_id' => $order->id,
        ];
        $this->withoutExceptionHandling();

        $this->post($route, $data)->assertOk();
        $order->refresh();
        $this->assertEquals($order->payout_id, $data['id']);
        $this->assertEquals($order->status, OrderPerformer::STATUS_PAYOUT);
    }
}
