<?php

namespace Client\API;

use App\Events\OrderCanceled;
use App\Events\OrderPerformerCanceled;
use App\Events\OrderPerformerReceived;
use App\Events\OrderReceived;
use App\Jobs\Client\Order\OrderStoredJob;
use App\Models\Order;
use App\Models\OrderPerformer;
use App\Models\User;
use Tests\Feature\Trait\PrepareForTestWithSeedTrait;
use Tests\TestCase;

class APIOrderTest extends TestCase
{

    use PrepareForTestWithSeedTrait;

    /**@test */
    public function test_a_order_can_be_viewed_any(): void
    {
        $user = User::query()->has('orders')->first();
        $route = route('back.api.orders.index');
        $jwt = $this->getJwt($user);

        $this->post($route, ['page' => 1])->assertUnauthorized();

        $this->withoutExceptionHandling();

        $this->withHeader('Authorization', $jwt)
            ->post($route, ['page' => 1])
            ->assertOk()
            ->assertJsonFragment(['current_page' => 1])
            ->assertJsonCount(7, 'data.0');
    }

    /**@test */
    public function test_a_order_can_be_viewed_with_premissions(): void
    {
        $user = User::query()->has('orders')->first();
        $order = $user->orders()->first();
        $another_order = Order::query()->firstWhere('user_id', '!=', $user->id);
        $route = route('back.api.orders.show', $order->id);
        $another_route = route('back.api.orders.show', $another_order->id);
        $jwt = $this->getJwt($user);

        $this->post($another_route)->assertUnauthorized();

        for ($i = 2; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->withHeader('Authorization', $jwt)->post($another_route)->assertForbidden();
        }

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->withHeader('Authorization', $jwt)
                ->post($route)
                ->assertOk()
                ->assertJsonCount(9);
        }
        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->withHeader('Authorization', $jwt)
            ->post($another_route)
            ->assertOk()
            ->assertJsonCount(9);
    }

    /**@test */
    public function test_a_order_can_be_stored_with_premissions(): void
    {
        $user = User::query()->first();
        $data = Order::factory()->raw();
        $data['return_url'] = fake()->url();
        foreach ($data['productTypes'] as $productType) $data['cart'][$productType['productType_id']] = $productType['amount'];
        $route = route('back.api.orders.store');
        $jwt = $this->getJwt($user);

        $this->post($route, $data)->assertUnauthorized();

        $this->withoutExceptionHandling();

        $res = $this->expectsJobs(OrderStoredJob::class)
            ->withHeader('Authorization', $jwt)
            ->post($route, $data)
            ->assertOk()
            ->getContent();
        $this->assertStringStartsWith('http', (string)$res);
        $count = collect((array)$data['productTypes'])->pluck('saler_id')->unique()->count();
        $order = $user->orders()->withcount('orderPerformers')->firstWhere('total_price', $data['total_price']);
        $this->assertModelExists($order)
            ->assertEquals($order->order_performers_count, $count);
    }

    /**@test */
    public function test_a_order_can_be_received_with_premissions(): void
    {
        $user = User::query()->has('orders')->first();
        $order = $user->orders()->first();
        $another_order = Order::query()->firstWhere('user_id', '!=', $user->id);
        $route = route('back.api.orders.update', $order->id);
        $another_route = route('back.api.orders.update', $another_order->id);
        $jwt = $this->getJwt($user);

        $this->patch($another_route)->assertUnauthorized();

        for ($i = 2; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->withHeader('Authorization', $jwt)
                ->patch($another_route)
                ->assertForbidden();
        }

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->expectsEvents([OrderReceived::class, OrderPerformerReceived::class])
                ->withHeader('Authorization', $jwt)
                ->patch($route)
                ->assertOk();
        }
        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->expectsEvents([OrderReceived::class, OrderPerformerReceived::class])
            ->withHeader('Authorization', $jwt)
            ->patch($another_route)
            ->assertOk();
    }

    /**@test */
    public function test_a_order_can_be_canceled_due_to_pay_with_premissions(): void
    {
        $user = User::query()->has('orders')->first();
        $order = $user->orders()->first();
        $another_order = Order::query()->firstWhere('user_id', '!=', $user->id);
        $route = route('back.api.orders.destroy', $order->id);
        $another_route = route('back.api.orders.destroy', $another_order->id);
        $jwt = $this->getJwt($user);

        $this->delete($another_route)->assertUnauthorized();

        for ($i = 2; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->withHeader('Authorization', $jwt)
                ->delete($another_route)
                ->assertForbidden();
        }

        $this->withoutExceptionHandling();

        session(['user.role' => User::ROLE_CLIENT]);
        $user->update(['role' => User::ROLE_CLIENT]);
        $this->withHeader('Authorization', $jwt)
            ->delete($route, ['due_to_pay' => true])
            ->assertOk();
        $this->assertSoftDeleted($order)
            ->assertFalse($order->orderPerformers()->exists());

        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->withHeader('Authorization', $jwt)
            ->delete($another_route, ['due_to_pay' => true])
            ->assertOk();
        $this->assertSoftDeleted($another_order)
            ->assertFalse($another_order->orderPerformers()->exists());
    }

    /**@test */
    public function test_a_order_performer_can_be_canceled_by_user_with_premissions(): void
    {
        $orderPerformer = OrderPerformer::query()
            ->has('order.orderPerformers', count: 2)
            ->first();
        $user = $orderPerformer->user()->first();
        $another_orderPerformer = OrderPerformer::query()
            ->has('order.orderPerformers', count: 2)
            ->firstWhere('user_id', '!=', $user->id);
        $route = route('back.api.orders.destroyOrderPerformer', $orderPerformer->id);
        $another_route = route('back.api.orders.destroyOrderPerformer', $another_orderPerformer->id);
        $jwt = $this->getJwt($user);

        $this->delete($another_route)
            ->assertUnauthorized();

        for ($i = 2; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->withHeader('Authorization', $jwt)
                ->delete($another_route)
                ->assertForbidden();
        }

        $this->withoutExceptionHandling();

        $this->withHeader('Authorization', $jwt)
            ->expectsEvents($orderPerformer->order()->exists() ? OrderPerformerCanceled::class : OrderCanceled::class)
            ->delete($route)
            ->assertOk();
        $this->assertSoftDeleted($orderPerformer)
            ->assertTrue($orderPerformer->order()->exists());

        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->withHeader('Authorization', $jwt)
            ->expectsEvents($another_orderPerformer->order()->exists() ? OrderPerformerCanceled::class : OrderCanceled::class)
            ->delete($another_route)
            ->assertOk();
        $this->assertSoftDeleted($another_orderPerformer)
            ->assertTrue($another_orderPerformer->order()->exists());
    }
}
