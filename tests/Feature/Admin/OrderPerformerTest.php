<?php

namespace Tests\Feature\Admin;

use App\Events\Order\OrderCanceled;
use App\Events\Order\OrderPerformerCanceled;
use App\Models\OrderPerformer;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Trait\StorageDbPrepareForTestTrait;
use Tests\TestCase;

class OrderPerformerTest extends TestCase
{

    use StorageDbPrepareForTestTrait;

    /**@test */
    public function test_a_order_can_be_viewed_any_with_premissions(): void
    {
        $user = User::query()->first();
        $route = route('admin.orders.index');

        $this->get($route)->assertNotFound();

        $user->role = User::ROLE_CLIENT;
        $user->save();
        $this->actingAs($user)->get($route)->assertNotFound();
        session()->flush();

        $this->withoutExceptionHandling();

        $user->role = User::ROLE_SALER;
        $user->save();
        $this->actingAs($user)->get($route)->assertViewIs('admin.order.index');
        session()->flush();

        $user->role = User::ROLE_ADMIN;
        $user->save();
        $this->actingAs($user)->get($route)->assertViewIs('admin.order.index');
    }

    /**@test */
    public function test_a_order_can_be_viewed_with_premissions(): void
    {
        $user = User::query()->has('orderPerformers')->first();
        $order = $user->orderPerformers()->first();
        $another_order = OrderPerformer::query()->firstWhere('saler_id', '!=', $user->id);
        $route = route('admin.orders.show', $order->id);
        $another_route = route('admin.orders.show', $another_order->id);

        $this->get($another_route)->assertNotFound();

        $user->role = User::ROLE_CLIENT;
        $user->save();
        $this->actingAs($user)->get($another_route)->assertNotFound();
        session()->flush();

        $user->role = User::ROLE_SALER;
        $user->save();
        $this->actingAs($user)->get($another_route)->assertForbidden();
        session()->flush();

        $this->withoutExceptionHandling();

        $this->actingAs($user)->get($route)->assertViewIs('admin.order.show');
        session()->flush();

        $user->role = User::ROLE_ADMIN;
        $user->save();
        $this->actingAs($user)->get($route)->assertViewIs('admin.order.show');
        session()->flush();

        $this->actingAs($user)->get($another_route)->assertViewIs('admin.order.show');
    }

    /**@test */
    public function test_a_order_can_be_sent_with_premissions(): void
    {
        $user = User::query()->has('orderPerformers')->first();
        $order = $user->orderPerformers()->first();
        $another_order = OrderPerformer::query()->firstWhere('saler_id', '!=', $user->id);
        $route = route('admin.orders.update', $order->id);
        $another_route = route('admin.orders.update', $another_order->id);

        $this->patch($another_route)->assertNotFound();

        $user->role = User::ROLE_CLIENT;
        $user->save();
        $this->actingAs($user)->patch($another_route)->assertNotFound();
        session()->flush();

        $user->role = User::ROLE_SALER;
        $user->save();
        $this->actingAs($user)->patch($another_route)->assertForbidden();
        session()->flush();

        $this->withoutExceptionHandling();

        $this->actingAs($user)->patch($route)->assertRedirect();
        $this->assertTrue(($order->status + 1) == $order->refresh()->status);
        session()->flush();

        $user->role = User::ROLE_ADMIN;
        $user->save();
        $order->status = OrderPerformer::STATUS_SENT;
        $order->save();
        $this->actingAs($user)
            ->patch($route)
            ->assertRedirect();
        $this->assertTrue(($order->status + 1) == $order->refresh()->status);
        session()->flush();

        $this->actingAs($user)->patch($another_route)->assertRedirect();
        $this->assertTrue(($another_order->status + 1) == $another_order->refresh()->status);
        session()->flush();
    }

    /**@test */
    public function test_a_order_can_be_canceled_with_premissions(): void
    {
        $user = User::query()->has('orderPerformers')->first();
        $order = $user->orderPerformers()->first();
        $another_order = OrderPerformer::query()->firstWhere('saler_id', '!=', $user->id);
        $route = route('admin.orders.destroy', $order->id);
        $another_route = route('admin.orders.destroy', $another_order->id);
        $from = route('admin.orders.show', $order->id);

        $this->delete($another_route)->assertNotFound();

        $user->role = User::ROLE_CLIENT;
        $user->save();
        $this->actingAs($user)->delete($another_route)->assertNotFound();
        session()->flush();

        $user->role = User::ROLE_SALER;
        $user->save();
        $this->actingAs($user)->delete($another_route)->assertForbidden();
        session()->flush();

//        $this->withoutExceptionHandling();

        $this->actingAs($user)
            ->from($from)
            ->expectsEvents($order->order()->exists() ? OrderPerformerCanceled::class : OrderCanceled::class)
            ->delete($route)
            ->assertRedirect($from);
        $this->assertSoftDeleted($order);
        $order->restore();
        $order->order()->restore();
        session()->flush();

        $user->role = User::ROLE_ADMIN;
        $user->save();
        $from = route('admin.orders.show', $order->id);
        $this->actingAs($user)
            ->from($from)
            ->expectsEvents($another_order->order()->exists() ? OrderPerformerCanceled::class : OrderCanceled::class)
            ->delete($another_route)
            ->assertRedirect($from);
        $this->assertSoftDeleted($order);
        session()->flush();
    }
}
