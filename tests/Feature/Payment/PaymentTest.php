<?php

namespace Payment;

use App\Models\OrderPerformer;
use App\Models\User;
use Tests\Feature\Trait\StorageDbPrepareForTestTrait;
use Tests\TestCase;

class PaymentTest extends TestCase
{

    use StorageDbPrepareForTestTrait;

    /**@test */
    public function test_a_order_can_be_paid(): void
    {
        /** @var User $user */
        $user = User::query()->withWhereHas('orders')->first();
        $order = $user->orders->first();
        $route = route('back.api.orders.payment', $order->id);

        $this->post($route)->assertUnauthorized();

        $this->withoutExceptionHandling();

        $this->actingAs($user)->get(route('home'));
        $this->withHeader('Authorization', session('jwt'))
            ->post($route)
            ->assertOk();
    }

    /**@test */
    public function test_a_order_can_be_refund(): void
    {
        /** @var User $user */
        $user = User::query()->withWhereHas('orders')->first();
        $order = $user->orders->first();
        $order->orderPerformers()->take(1)->delete();
        $route = route('back.api.orders.refund', $order->id);
        $this->post($route)->assertUnauthorized();

        $this->actingAs($user)->get(route('home'));
        $this->withHeader('Authorization', session('jwt'))
            ->post($route)
            ->assertOk();
    }

    /**@test */
    public function test_a_order_can_be_payout(): void
    {
        $orderPerformer = OrderPerformer::query()->first();
        /** @var User $user */
        $user = $orderPerformer->saler()->first();
        $another_orderPerformer = OrderPerformer::query()->firstWhere('saler_id', '!=', $user->id);

        $route = route('admin.orders.payout', $orderPerformer->id);
        $another_route = route('admin.orders.payout', $another_orderPerformer->id);
        $from = route('admin.orders.show', $orderPerformer->id);
        $another_from = route('admin.orders.show', $another_orderPerformer->id);

        $this->post($route)->assertNotFound();

        $user->role = User::ROLE_CLIENT;
        $user->save();
        $this->actingAs($user)->post($another_route)->assertNotFound();
        session()->flush();

        $user->role = User::ROLE_SALER;
        $user->save();
        $this->actingAs($user)->post($another_route)->assertForbidden();
        session()->flush();

        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)
                ->from($from)
                ->post($route)
                ->assertRedirect($from);
            session()->flush();
        }
        $user->role = User::ROLE_ADMIN;
        $user->save();
        $this->actingAs($user)
            ->from($another_from)
            ->post($another_route)
            ->assertRedirect($another_from);
    }
}
