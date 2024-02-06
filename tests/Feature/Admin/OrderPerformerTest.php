<?php

namespace Tests\Feature\Admin;

use App\Events\OrderCanceled;
use App\Events\OrderPerformerCanceled;
use App\Models\OrderPerformer;
use App\Models\User;
use Tests\Feature\Trait\PrepareForTestWithSeedTrait;
use Tests\TestCase;

class OrderPerformerTest extends TestCase
{

    use PrepareForTestWithSeedTrait;

    /**@test */
    public function test_a_order_performer_can_be_viewed_any_with_premissions(): void
    {
        $user = User::query()->first();
        $route = route('admin.orders.index');

        $this->get($route)->assertNotFound();

        session(['user.role' => User::ROLE_CLIENT]);
        $user->update(['role' => User::ROLE_CLIENT]);
        $this->actingAs($user)->get($route)->assertNotFound();

        $this->withoutExceptionHandling();

        session(['user.role' => User::ROLE_SALER]);
        $user->update(['role' => User::ROLE_SALER]);
        $this->actingAs($user)->get($route)->assertViewIs('admin.order.index');

        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)->get($route)->assertViewIs('admin.order.index');
    }

    /**@test */
    public function test_a_order_performer_can_be_viewed_with_premissions(): void
    {
        $user = User::query()->has('orderPerformers')->first();
        $order = $user->orderPerformers()->first();
        $another_order = OrderPerformer::query()->firstWhere('saler_id', '!=', $user->id);
        $route = route('admin.orders.show', $order->id);
        $another_route = route('admin.orders.show', $another_order->id);

        $this->get($another_route)->assertNotFound();

        session(['user.role' => User::ROLE_CLIENT]);
        $user->update(['role' => User::ROLE_CLIENT]);
        $this->actingAs($user)->get($another_route)->assertNotFound();

        session(['user.role' => User::ROLE_SALER]);
        $user->update(['role' => User::ROLE_SALER]);
        $this->actingAs($user)->get($another_route)->assertForbidden();

        $this->withoutExceptionHandling();

        $this->actingAs($user)->get($route)->assertViewIs('admin.order.show');
        $this->actingAs($user)->get($route)->assertViewIs('admin.order.show');

        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)->get($route)->assertViewIs('admin.order.show');

        $this->actingAs($user)->get($another_route)->assertViewIs('admin.order.show');
    }

    /**@test */
    public function test_a_order_performer_can_be_sent_with_premissions(): void
    {
        $user = User::query()->has('orderPerformers')->first();
        $order = $user->orderPerformers()->first();
        $another_order = OrderPerformer::query()->firstWhere('saler_id', '!=', $user->id);
        $route = route('admin.orders.update', $order->id);
        $another_route = route('admin.orders.update', $another_order->id);

        $this->patch($another_route)->assertNotFound();

        session(['user.role' => User::ROLE_CLIENT]);
        $user->update(['role' => User::ROLE_CLIENT]);
        $this->actingAs($user)->patch($another_route)->assertNotFound();

        session(['user.role' => User::ROLE_SALER]);
        $user->update(['role' => User::ROLE_SALER]);
        $this->actingAs($user)->patch($another_route)->assertForbidden();

        $this->withoutExceptionHandling();

        $this->actingAs($user)
            ->patch($route)
            ->assertRedirect();
        $this->assertEquals(($order->status + 1), $order->refresh()->status);

        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $order->decrement('status');
        $order->refresh();
        $this->actingAs($user)
            ->patch($route)
            ->assertRedirect();
        $this->assertEquals(($order->status + 1), $order->refresh()->status);

        $this->actingAs($user)
            ->patch($another_route)
            ->assertRedirect();
        $this->assertEquals(($another_order->status + 1), $another_order->refresh()->status);
    }

    /**@test */
    public function test_a_order_performer_can_be_canceled_with_premissions(): void
    {
        $user = User::query()->has('orderPerformers')->first();
        $order = $user->orderPerformers()->first();
        $another_order = OrderPerformer::query()->firstWhere('saler_id', '!=', $user->id);
        $route = route('admin.orders.destroy', $order->id);
        $another_route = route('admin.orders.destroy', $another_order->id);
        $from = route('admin.orders.show', $order->id);

        $this->delete($another_route)->assertNotFound();

        session(['user.role' => User::ROLE_CLIENT]);
        $user->update(['role' => User::ROLE_CLIENT]);
        $this->actingAs($user)->delete($another_route)->assertNotFound();

        session(['user.role' => User::ROLE_SALER]);
        $user->update(['role' => User::ROLE_SALER]);
        $this->actingAs($user)->delete($another_route)->assertForbidden();

        $this->withoutExceptionHandling();

        $this->actingAs($user)
            ->from($from)
            ->expectsEvents($order->order()->exists() ? OrderPerformerCanceled::class : OrderCanceled::class)
            ->delete($route)
            ->assertRedirect($from);
        $this->assertSoftDeleted($order);
        $order->restore();
        $order->order()->restore();

        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $from = route('admin.orders.show', $order->id);
        $this->actingAs($user)
            ->from($from)
            ->expectsEvents($another_order->order()->exists() ? OrderPerformerCanceled::class : OrderCanceled::class)
            ->delete($another_route)
            ->assertRedirect($from);
        $this->assertSoftDeleted($order);
    }
}
