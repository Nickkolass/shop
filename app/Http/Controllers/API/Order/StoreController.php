<?php

namespace App\Http\Controllers\API\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Order\StoreRequest;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StoreController extends Controller
{
    public function __invoke(StoreRequest $request)
    {

        $data = $request->validated();

        DB::beginTransaction();
        try {
            $password = Hash::make('123123123');

            $user = User::firstOrCreate([
                'email' => $data['email']
            ], [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $password,
            ]);

            $order = Order::create([
                'user_id' => $user->id,
                'products' => json_encode($data['products']),
                'total_price' => $data['total_price'],
                'payment_status' => $data['payment_status'],
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }

        return new OrderResource($order);
    }
}
