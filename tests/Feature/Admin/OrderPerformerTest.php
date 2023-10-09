<?php

namespace Tests\Feature\Admin;

use App\Models\OrderPerformer;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderPerformerTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    protected function tearDown(): void
    {
        foreach (Storage::directories() as $dir) if ($dir != 'factory') Storage::deleteDirectory($dir);
        parent::tearDown();
    }

    /**@test */
    public function test_a_order_can_be_viewed_any_with_premissions(): void
    {
        $user = User::query()->first();

        $this->get(route('admin.orders.index'))->assertNotFound();

        $user->role = 3;
        $user->save();
        $this->actingAs($user)->get(route('admin.orders.index'))->assertNotFound();
        session()->flush();

        $this->withoutExceptionHandling();

        $user->role = 2;
        $user->save();
        $this->actingAs($user)->get(route('admin.orders.index'))->assertViewIs('admin.order.index');
        session()->flush();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('admin.orders.index'))->assertViewIs('admin.order.index');
    }

    /**@test */
    public function test_a_order_can_be_viewed_with_premissions(): void
    {
        $user = User::query()->first();
        $order = $user->orderPerformers()->first();
        $another_order = OrderPerformer::query()->firstWhere('saler_id', '!=', $user->id);

        $this->get(route('admin.orders.show', $order->id))->assertNotFound();

        $user->role = 3;
        $user->save();
        $this->actingAs($user)->get(route('admin.orders.show', $order->id))->assertNotFound();
        session()->flush();

        $user->role = 2;
        $user->save();
        $this->actingAs($user)->get(route('admin.orders.show', $another_order->id))->assertForbidden();
        session()->flush();

        $this->withoutExceptionHandling();

        $this->actingAs($user)->get(route('admin.orders.show', $order->id))->assertViewIs('admin.order.show');
        session()->flush();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('admin.orders.show', $order->id))->assertViewIs('admin.order.show');
        session()->flush();

        $this->actingAs($user)->get(route('admin.orders.show', $another_order->id))->assertViewIs('admin.order.show');
    }

    /**@test */
    public function test_a_order_can_be_sent_with_premissions(): void
    {
        $user = User::query()->first();
        $order = $user->orderPerformers()->first();
        $another_order = OrderPerformer::query()->firstWhere('saler_id', '!=', $user->id);

        $this->patch(route('admin.orders.update', $order->id))->assertNotFound();

        $user->role = 3;
        $user->save();
        $this->actingAs($user)->patch(route('admin.orders.update', $order->id))->assertNotFound();
        session()->flush();

        $user->role = 2;
        $user->save();
        $this->actingAs($user)->patch(route('admin.orders.update', $another_order->id))->assertForbidden();
        session()->flush();

        $this->withoutExceptionHandling();

        $this->actingAs($user)->patch(route('admin.orders.update', $order->id))->assertRedirect();
        $this->assertTrue($order->refresh()->status != 'В работе');
        session()->flush();

        $user->role = 1;
        $user->save();
        $order->status = 'В работе';
        $order->save();
        $this->actingAs($user)->patch(route('admin.orders.update', $order->id))->assertRedirect();
        $this->assertTrue($order->refresh()->status != 'В работе');
        session()->flush();

        $this->actingAs($user)->patch(route('admin.orders.update', $another_order->id))->assertRedirect();
        $this->assertTrue($another_order->refresh()->status != 'В работе');
        session()->flush();
    }

    /**@test */
    public function test_a_order_can_be_canceled_with_premissions(): void
    {
        $user = User::query()->first();
        $order = $user->orderPerformers()->first();
        $another_order = OrderPerformer::query()->firstWhere('saler_id', '!=', $user->id);

        $this->delete(route('admin.orders.destroy', $another_order->id))->assertNotFound();

        $user->role = 3;
        $user->save();
        $this->actingAs($user)->delete(route('admin.orders.destroy', $another_order->id))->assertNotFound();
        session()->flush();

        $user->role = 2;
        $user->save();
        $this->actingAs($user)->delete(route('admin.orders.destroy', $another_order->id))->assertForbidden();
        session()->flush();

        $this->withoutExceptionHandling();

        $this->actingAs($user)->delete(route('admin.orders.destroy', $order->id))->assertRedirect(route('admin.orders.show', $order->id));
        $this->assertNotNull($order->refresh()->deleted_at);
        session()->flush();

        $user->role = 1;
        $user->save();
        $order = $user->orderPerformers()->first();
        $this->actingAs($user)->delete(route('admin.orders.destroy', $order->id))->assertRedirect(route('admin.orders.show', $order->id));
        $this->assertNotNull($order->refresh()->deleted_at);
        session()->flush();

        $this->actingAs($user)->delete(route('admin.orders.destroy', $another_order->id))->assertRedirect(route('admin.orders.show', $another_order->id));
        $this->assertNotNull($another_order->refresh()->deleted_at);
        session()->flush();
    }
}
