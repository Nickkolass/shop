<?php

namespace Client;

use App\Events\OrderCanceled;
use App\Events\OrderPaid;
use App\Events\OrderPerformerCanceled;
use App\Events\OrderPerformerPaid;
use App\Events\OrderPerformerReceived;
use App\Events\OrderReceived;
use App\Jobs\Client\Order\OrderStoredJob;
use App\Models\Order;
use App\Models\OrderPerformer;
use App\Notifications\Order\OrderNotificationSubscriber;
use App\Services\Client\API\PaymentService;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\Trait\PrepareForTestWithSeedTrait;
use Tests\TestCase;

class OrderEventTest extends TestCase
{

    use PrepareForTestWithSeedTrait;

    /**@test */
    public function test_order_stored_job(): void
    {
        $this->withoutExceptionHandling();
        $order = Order::query()->first();
        app(OrderStoredJob::class, ['order' => $order])->handle();
        $this->assertSoftDeleted($order->fresh());
        $this->assertFalse($order->orderPerformers()->exists());
    }

    /**@test */
    public function test_order_paid_event(): void
    {
        Queue::fake();
        Event::fake(OrderPerformerPaid::class);
        $order = Order::query()->withCount('orderPerformers')->first();
        $this->withoutExceptionHandling();

        event(new OrderPaid($order));
        Event::assertDispatched(OrderPerformerPaid::class, $order->order_performers_count);
        Queue::assertPushed(CallQueuedListener::class, function ($job) {
            return $job->class == OrderNotificationSubscriber::class && $job->method == 'handleOrderPaid';
        });
    }

    /**@test */
    public function test_order_performer_paid_event(): void
    {
        Queue::fake();
        $order = OrderPerformer::query()->first();
        $this->withoutExceptionHandling();
        event(new OrderPerformerPaid($order));
        Queue::assertPushed(CallQueuedListener::class, function ($job) {
            return $job->class == OrderNotificationSubscriber::class && $job->method == 'handleOrderPerformerPaid';
        });
    }

    /**@test */
    public function test_order_received_event(): void
    {
        Queue::fake();
        Event::fake(OrderPerformerReceived::class);
        $spy = $this->spy(PaymentService::class);

        $this->withoutExceptionHandling();
        $orders = Order::query()->has('orderPerformers', count: 2)->withCount('orderPerformers')->take(2)->get();
        /** @var Order $order */
        $order = $orders->pop();
        /** @var Order $another_order */
        $another_order = $orders->pop();
        $another_order->orderPerformers()->take(1)->delete();
        $another_order->setAttribute('order_performers_count', $another_order->order_performers_count - 1);

        // получение всего заказа
        event(new OrderReceived($order));

        Event::assertDispatched(OrderPerformerReceived::class, $order->order_performers_count);
        Queue::assertPushed(CallQueuedListener::class, function ($job) {
            return $job->class == OrderNotificationSubscriber::class && $job->method == 'handleOrderReceived';
        });
        $spy->shouldNotHaveReceived('refund');
        $spy->shouldNotHaveReceived('payout');

        // получение заказа при наличии отменной позиции
        event(new OrderReceived($another_order));
        Event::assertDispatched(OrderPerformerReceived::class, $order->order_performers_count + $another_order->order_performers_count);
        Queue::assertPushed(CallQueuedListener::class, 2);
        Queue::assertPushed(CallQueuedListener::class, function ($job) {
            return $job->class == OrderNotificationSubscriber::class && $job->method == 'handleOrderReceived';
        });
        $spy->shouldHaveReceived('refund');
        $spy->shouldNotHaveReceived('payout');
    }

    /**@test */
    public function test_order_performer_received_event(): void
    {
        Queue::fake();
        $this->withoutExceptionHandling();
        $order = OrderPerformer::query()->first();
        $spy = $this->spy(PaymentService::class);

        event(new OrderPerformerReceived($order));
        Queue::assertPushed(CallQueuedListener::class, function ($job) {
            return $job->class == OrderNotificationSubscriber::class && $job->method == 'handleOrderPerformerReceived';
        });
        $spy->shouldHaveReceived('payout');
        $spy->shouldNotHaveReceived('refund');
    }

    /**@test */
    public function test_order_canceled_event(): void
    {
        Queue::fake();
        Event::fake(OrderPerformerCanceled::class);
        $this->withoutExceptionHandling();
        $orders = Order::query()
            ->withWhereHas('orderPerformers', null, '>=', 2)
            ->take(2)
            ->get();
        /** @var Order $order */
        $order = $orders->first();
        /** @var Order $another_order */
        $another_order = $orders->last();
        $another_order->orderPerformers()->delete();

        // при отказе от неоплаченного заказа событие не запускается
        // отказ от оплаченного заказа при отправке отдельных нарядов
        $order->orderPerformers->pop();
        $deleted_ids = $order->orderPerformers->pluck('id')->all();
        OrderPerformer::query()->whereIn('id', $deleted_ids)->delete();

        $spy = $this->spy(PaymentService::class);
        event(new OrderCanceled($order, $deleted_ids));
        Queue::assertPushed(CallQueuedListener::class, function ($job) {
            return $job->class == OrderNotificationSubscriber::class && $job->method == 'handleOrderCanceled';
        });
        Event::assertDispatched(OrderPerformerCanceled::class, count($deleted_ids));
        $spy->shouldNotHaveReceived('payout');
        $spy->shouldHaveReceived('refund');

        // полный отказ от оплаченного заказа
        $deleted_ids2 = $another_order->orderPerformers->pluck('id')->all();
        $spy = $this->spy(PaymentService::class);
        event(new OrderCanceled($another_order, $deleted_ids2));
        Queue::assertPushed(CallQueuedListener::class, 2);
        Queue::assertPushed(CallQueuedListener::class, function ($job) {
            return $job->class == OrderNotificationSubscriber::class && $job->method == 'handleOrderCanceled';
        });
        Event::assertDispatched(OrderPerformerCanceled::class, count($deleted_ids) + count($deleted_ids2));
        $spy->shouldNotHaveReceived('payout');
        $spy->shouldHaveReceived('refund');
    }

    /**@test */
    public function test_order_performer_canceled_event(): void
    {
        Queue::fake();
        $this->withoutExceptionHandling();
        $order = OrderPerformer::query()->has('order.orderPerformers', '>=', 2)->first();
        $spy = $this->spy(PaymentService::class);

        event(new OrderPerformerCanceled($order, false));
        Queue::assertPushed(CallQueuedListener::class, function ($job) {
            return $job->class == OrderNotificationSubscriber::class && $job->method == 'handleOrderPerformerCanceled';
        });
        $spy->shouldNotHaveReceived('refund');
        $spy->shouldNotHaveReceived('payout');
    }
}
