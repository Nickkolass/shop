<?php

namespace Job;

use App\Components\Payment\PaymentClientInterface;
use App\Events\Order\OrderCanceled;
use App\Events\Order\OrderPaid;
use App\Events\Order\OrderPerformerCanceled;
use App\Events\Order\OrderPerformerPaid;
use App\Events\Order\OrderPerformerReceived;
use App\Events\Order\OrderReceived;
use App\Jobs\Client\Order\OrderStoredJob;
use App\Jobs\Scheduler\DBCleanUpdateJob;
use App\Mail\MailOrderStoredReceivedCanceled;
use App\Mail\MailWelcomeQueue;
use App\Models\Order;
use App\Models\OrderPerformer;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use Illuminate\Testing\PendingCommand;
use Tests\Feature\Trait\StorageDbPrepareForTestTrait;
use Tests\TestCase;

class JobTest extends TestCase
{

    use StorageDbPrepareForTestTrait;

    /**@test */
    public function test_db_clean_update_job(): void
    {
        $this->withoutExceptionHandling();
        /** @var PendingCommand $res */
        $res = $this->expectsJobs(DBCleanUpdateJob::class)
            ->artisan('db:clean');
        $res->assertOk();
        $this->assertTrue(cache()->has('categories'));
    }

    /**@test */
    public function test_order_stored_job(): void
    {
        $this->withoutExceptionHandling();
        $order = Order::query()->first();
        app(OrderStoredJob::class, ['order' => $order])->handle();
        $this->assertSoftDeleted($order);
        $this->assertFalse($order->orderPerformers()->exists());
    }

    /**@test */
    public function test_user_registered_event(): void
    {
        Mail::fake();
        $user = User::query()->first();
        $this->withoutExceptionHandling();
        event(new Registered($user));
        Mail::assertQueued(MailWelcomeQueue::class);
    }

    /**@test */
    public function test_order_paid_event(): void
    {
        Mail::fake();
        $order = Order::query()->withCount('orderPerformers')->first();
        session(['user.email' => '123@mail.ru']);
        $this->withoutExceptionHandling();
        event(new OrderPaid($order, uniqid('', true)));
        Mail::assertQueued(MailOrderStoredReceivedCanceled::class, $order->order_performers_count + 1);
    }

    /**@test */
    public function test_order_performer_paid_event(): void
    {
        Mail::fake();
        $order = OrderPerformer::query()->first();
        $this->withoutExceptionHandling();
        event(new OrderPerformerPaid($order));
        Mail::assertQueued(MailOrderStoredReceivedCanceled::class, 1);
    }

    /**@test */
    public function test_order_received_event(): void
    {
        Mail::fake();
        $spy = $this->spy(PaymentClientInterface::class);
        $this->withoutExceptionHandling();
        $order = Order::query()->withCount('orderPerformers')->first();
        session(['user.email' => '123@mail.ru']);

        // получение всего заказа
        event(new OrderReceived($order));
        Mail::assertQueued(MailOrderStoredReceivedCanceled::class, $order->order_performers_count + 1);
        $spy->shouldNotHaveReceived('refund');
        $spy->shouldHaveReceived('payout');

        // получение заказа при наличии отменной позиции
        $order->orderPerformers()->take(1)->delete();
        event(new OrderReceived($order));
        Mail::assertQueued(MailOrderStoredReceivedCanceled::class, $order->order_performers_count - 1 + $order->order_performers_count + 1 + 1);
        $spy->shouldHaveReceived('refund');
        $spy->shouldHaveReceived('payout');
    }

    /**@test */
    public function test_order_performer_received_event(): void
    {
        Mail::fake();
        $spy = $this->spy(PaymentClientInterface::class);
        $this->withoutExceptionHandling();
        $order = OrderPerformer::query()->first();

        event(new OrderPerformerReceived($order));
        Mail::assertQueued(MailOrderStoredReceivedCanceled::class, 1);
        $spy->shouldNotHaveReceived('refund');
        $spy->shouldHaveReceived('payout');
    }

    /**@test */
    public function test_order_canceled_event(): void
    {
        Mail::fake();
        $spy = $this->spy(PaymentClientInterface::class);
        session(['user.email' => '123@mail.ru']);
        $this->withoutExceptionHandling();

        // отказ от неоплаченного заказа
        /** @var Order $order */
        $order = Order::query()->withWhereHas('orderPerformers', null, '>=', 2)->first();
        event(new OrderCanceled($order, $order->orderPerformers->pluck('id')->all()));
        Mail::assertQueued(MailOrderStoredReceivedCanceled::class, 1);
        $spy->shouldNotHaveReceived('payout');
        $spy->shouldNotHaveReceived('refund');

        // отказ от оплаченного заказа при отправке продавцом части заказа
        $order->refresh()->orderPerformers->pop();
        $deleted_ids = $order->orderPerformers->pluck('id')->all();
        OrderPerformer::query()->whereIn('id', $deleted_ids)->delete();
        event(new OrderCanceled($order, $deleted_ids));
        Mail::assertQueued(MailOrderStoredReceivedCanceled::class, count($deleted_ids) + 1 + 1);
        $spy->shouldNotHaveReceived('payout');
        $spy->shouldHaveReceived('refund');

        // полный отказ от оплаченного заказа
        $order->orderPerformers()->delete();
        $deleted_ids2 = $order->orderPerformers()->withTrashed()->pluck('id')->all();
        event(new OrderCanceled($order, $deleted_ids2));
        Mail::assertQueued(MailOrderStoredReceivedCanceled::class, count($deleted_ids2) + 1 + count($deleted_ids) + 1 + 1);
        $spy->shouldNotHaveReceived('payout');
        $spy->shouldHaveReceived('refund');
    }

    /**@test */
    public function test_order_performer_canceled_event(): void
    {
        Mail::fake();
        $spy = $this->spy(PaymentClientInterface::class);
        $this->withoutExceptionHandling();
        $order = OrderPerformer::query()->has('order.orderPerformers', '>=', 2)->first();

        event(new OrderPerformerCanceled($order, false));
        Mail::assertQueued(MailOrderStoredReceivedCanceled::class, 1);
        $spy->shouldNotHaveReceived('payout');
        $spy->shouldNotHaveReceived('refund');
    }
}
