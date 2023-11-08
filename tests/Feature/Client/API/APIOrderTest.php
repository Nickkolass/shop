<?php

namespace Client\API;

use App\Events\Order\OrderCanceled;
use App\Events\Order\OrderPerformerCanceled;
use App\Events\Order\OrderPerformerReceived;
use App\Events\Order\OrderReceived;
use App\Jobs\Client\Order\OrderStoredJob;
use App\Models\Order;
use App\Models\OrderPerformer;
use App\Models\User;
use Tests\Feature\Trait\StorageDbPrepareForTestTrait;
use Tests\TestCase;

class APIOrderTest extends TestCase
{

    use StorageDbPrepareForTestTrait;

    /**@test */
    public function test_a_order_can_be_viewed_any(): void
    {
        $user = User::query()->has('orders')->first();
        $route = route('back.api.orders.index');

        $this->post($route, ['page' => 1])->assertUnauthorized();

        $this->withoutExceptionHandling();

        $this->actingAs($user)->get(route('home'));
        $this->withHeader('Authorization', session('jwt'))
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

        $this->post($another_route)->assertUnauthorized();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('home'));
            $this->withHeader('Authorization', session('jwt'))->post($another_route)->assertForbidden();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('home'));
            $this->withHeader('Authorization', session('jwt'))
                ->post($route)
                ->assertOk()
                ->assertJsonCount(9);
            session()->flush();
        }
        $user->role = User::ROLE_ADMIN;
        $user->save();
        $this->actingAs($user)->get(route('home'));
        $this->withHeader('Authorization', session('jwt'))->post($another_route)
            ->assertOk()
            ->assertJsonCount(9);
    }

    /**@test */
    public function test_a_order_can_be_stored_with_premissions(): void
    {
        $user = User::query()->first();
        $data = Order::factory()->raw();
        $data['user_id'] = $user->id;
        foreach ($data['productTypes'] as $productType) $data['cart'][$productType['productType_id']] = $productType['amount'];
        $route = route('back.api.orders.store');

        $this->post($route, $data)->assertUnauthorized();

        $this->withoutExceptionHandling();

        $this->actingAs($user)->get(route('home'));
        $this->expectsJobs(OrderStoredJob::class)
            ->withHeader('Authorization', session('jwt'))
            ->post($route, $data)
            ->assertOk();
        $count = collect((array)$data['productTypes'])->pluck('saler_id')->unique()->count();
        $this->assertModelExists($order = $user->orders()->with('orderPerformers')->firstWhere('total_price', $data['total_price']))
            ->assertEquals($order->orderPerformers->count(), $count);
    }

    /**@test */
    public function test_a_order_can_be_received_with_premissions(): void
    {
        $user = User::query()->has('orders')->first();
        $order = $user->orders()->first();
        $another_order = Order::query()->firstWhere('user_id', '!=', $user->id);
        $route = route('back.api.orders.update', $order->id);
        $another_route = route('back.api.orders.update', $another_order->id);

        $this->patch($another_route)->assertUnauthorized();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('home'));
            $this->withHeader('Authorization', session('jwt'))
                ->patch($another_route)
                ->assertForbidden();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('home'));
            $this->expectsEvents([OrderReceived::class, OrderPerformerReceived::class])
                ->withHeader('Authorization', session('jwt'))
                ->patch($route)
                ->assertOk();
            session()->flush();
        }
        $user->role = User::ROLE_ADMIN;
        $user->save();
        $this->actingAs($user)->get(route('home'));
        $this->expectsEvents([OrderReceived::class, OrderPerformerReceived::class])
            ->withHeader('Authorization', session('jwt'))
            ->patch($another_route)
            ->assertOk();
    }

    /**@test */
    public function test_a_order_can_be_canceled_due_to_payment_with_premissions(): void
    {
        $user = User::query()->has('orders')->first();
        $order = $user->orders()->first();
        $another_order = Order::query()->firstWhere('user_id', '!=', $user->id);
        $route = route('back.api.orders.destroy', $order->id);
        $another_route = route('back.api.orders.destroy', $another_order->id);

        $this->delete($another_route)->assertUnauthorized();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('home'));
            $this->withHeader('Authorization', session('jwt'))
                ->delete($another_route)
                ->assertForbidden();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = User::ROLE_CLIENT;
        $user->save();
        $this->actingAs($user)->get(route('home'));
        $this->withHeader('Authorization', session('jwt'))
            ->delete($route, ['due_to_payment' => true])
            ->assertOk();
        $this->assertSoftDeleted($order)
            ->assertFalse($order->orderPerformers()->exists());
        session()->flush();

        $user->role = User::ROLE_ADMIN;
        $user->save();
        $this->actingAs($user)->get(route('home'));
        $this->withHeader('Authorization', session('jwt'))
            ->delete($another_route, ['due_to_payment' => true])
            ->assertOk();
        $this->assertSoftDeleted($another_order)
            ->assertFalse($another_order->orderPerformers()->exists());
    }

    /**@test */
    public function test_a_orderPerformer_can_be_canceled_by_user_with_premissions(): void
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

        $this->delete($another_route)
            ->assertUnauthorized();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('home'));
            $this->withHeader('Authorization', session('jwt'))
                ->delete($another_route)
                ->assertForbidden();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $this->actingAs($user)->get(route('home'));
        $this->withHeader('Authorization', session('jwt'))
            ->expectsEvents($orderPerformer->order()->exists() ? OrderPerformerCanceled::class : OrderCanceled::class)
            ->delete($route)
            ->assertOk();
        $this->assertSoftDeleted($orderPerformer)
            ->assertTrue($orderPerformer->order()->exists());
        session()->flush();

        $user->role = User::ROLE_ADMIN;
        $user->save();
        $this->actingAs($user)->get(route('home'));
        $this->withHeader('Authorization', session('jwt'))
            ->expectsEvents($another_orderPerformer->order()->exists() ? OrderPerformerCanceled::class : OrderCanceled::class)
            ->delete($another_route)
            ->assertOk();
        $this->assertSoftDeleted($another_orderPerformer)
            ->assertTrue($another_orderPerformer->order()->exists());
    }
}
