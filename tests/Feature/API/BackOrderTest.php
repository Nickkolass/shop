<?php

namespace Tests\Feature\API;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderPerformer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class BackOrderTest extends TestCase
{

    use RefreshDatabase;

    /**@test */
    public function test_a_order_can_be_viewed_any()
    {
        $this->seed();
        View::share('categories', Category::all()->toArray());
        $user = User::first();

        $this->post('/api/orders', ['page' => 1])->assertUnauthorized();

        $this->withoutExceptionHandling();

        $this->actingAs($user)->get('/');
        $res = $this->withHeader('Authorization', session('jwt'))->post('/api/orders', ['page' => 1]);
        $res->assertOk();
        $res->assertJsonFragment(['current_page' => 1]);
        $res->assertJsonCount(7, 'data.0');

    }

    /**@test */
    public function test_a_order_can_be_viewed_with_premissions()
    {
        $this->seed();
        View::share('categories', Category::all()->toArray());
        $user = User::first();
        if (empty($order = $user->orders()->first())) {
            $user = User::latest('id')->first();
            $order = $user->orders()->first();
        }
        $another_order = Order::where('user_id', '!=', $user->id)->first();

        $this->post('/api/orders/' . $order->id)->assertUnauthorized();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get('/');
            $this->withHeader('Authorization', session('jwt'))->post('/api/orders/' . $another_order->id)->assertForbidden();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get('/');
            $res = $this->withHeader('Authorization', session('jwt'))->post('/api/orders/' . $order->id);
            $res->assertOk();
            $res->assertJsonCount(7);
            session()->flush();
        }
        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get('/');
        $res = $this->withHeader('Authorization', session('jwt'))->post('/api/orders/' . $another_order->id);
        $res->assertOk();
        $res->assertJsonCount(7);
    }

    /**@test */
    public function test_a_order_can_be_stored_with_premissions()
    {
        $this->seed();
        View::share('categories', Category::all()->toArray());
        $user = User::first();
        $data = Order::factory()->raw();
        $data['user_id'] = $user->id;
        foreach ($data['productTypes'] as &$productType) $data['cart'][$productType['productType_id']] = $productType['amount'];

        $this->post('/api/orders/store', $data)->assertUnauthorized();

        $this->withoutExceptionHandling();

        $order_performers_count = OrderPerformer::count();
        $this->actingAs($user)->get('/');
        $this->withHeader('Authorization', session('jwt'))->post('/api/orders/store', $data)->assertOk();
        $count = collect($data['productTypes'])->pluck('saler_id')->unique()->count();
        $this->assertDatabaseCount('order_performers', $order_performers_count + $count);
        $this->assertDatabaseCount('jobs', 1);
    }

    /**@test */
    public function test_a_order_can_be_accepted_with_premissions()
    {
        $this->seed();
        View::share('categories', Category::all()->toArray());
        $user = User::first();
        $order = $user->orders()->first();
        $another_order = Order::where('user_id', '!=', $user->id)->first();

        $this->patch('/api/orders/' . $another_order->id)->assertUnauthorized();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get('/');
            $this->withHeader('Authorization', session('jwt'))->patch('/api/orders/' . $another_order->id)->assertForbidden();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get('/');
            $this->withHeader('Authorization', session('jwt'))->patch('/api/orders/' . $order->id)->assertOk();
            session()->flush();
        }
        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get('/');
        $this->withHeader('Authorization', session('jwt'))->patch('/api/orders/' . $another_order->id)->assertOk();
    }

    /**@test */
    public function test_a_order_can_be_canceled_with_premissions()
    {
        $this->seed();
        View::share('categories', Category::all()->toArray());
        $user = User::first();
        $order = $user->orders()->first();
        $another_order = Order::where('user_id', '!=', $user->id)->first();

        $this->delete('/api/orders/' . $another_order->id)->assertUnauthorized();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get('/');
            $this->withHeader('Authorization', session('jwt'))->delete('/api/orders/' . $another_order->id)->assertForbidden();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get('/');
            $this->withHeader('Authorization', session('jwt'))->delete('/api/orders/' . $order->id)->assertOk();
            $this->assertTrue($order->orderPerformers()->count() == 0);
            $order->refresh()->restore();
            $order->orderPerformers()->restore();
            session()->flush();
        }
        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get('/');
        $this->withHeader('Authorization', session('jwt'))->delete('/api/orders/' . $another_order->id)->assertOk();
        $this->assertTrue($another_order->orderPerformers()->count() == 0);
    }
}
