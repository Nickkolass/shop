<?php

namespace App\Services\Client\API\Order;

use App\Components\Yookassa\YooKassaClient;
use App\Events\Order\OrderCanceled;
use App\Events\Order\OrderStored;
use App\Events\Order\Payment;
use App\Models\Order;
use App\Models\OrderPerformer;
use App\Models\ProductType;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderDBService
{
    /**
     * @param array<mixed> $data
     * @return string $payment_url
     */
    public function store(array $data): string
    {
        DB::beginTransaction();
        $url = $this->productCountUpdate($data['cart'])->orderStore($data)->payment($data['order']);
        DB::commit();
        return $url;
    }

    /**
     * @param Order $order
     * @return string $payment_url
     */
    public function payment(Order $order): string
    {
        $payment = YooKassaClient::make()->payment($order);
        event(new Payment($order, $payment['payment_id']));
        return $payment['payment_url'];
    }

    /**
     * @param array<mixed> $data
     * @return self
     */
    private function orderStore(array &$data): self
    {
        $user = auth('api')->user();
        /** @var User $user */

        $data['order'] = Order::query()->create([
            'user_id' => $user->id,
            'productTypes' => array_values($data['cart']),
            'delivery' => $data['delivery'] . ". Получатель: $user->surname $user->name $user->patronymic. Адрес: $user->address",
            'total_price' => $data['total_price'],
        ]);
        return $this;
    }

    /**
     * @param array<int> &$cart
     * @return self
     */
    private function productCountUpdate(array &$cart): self
    {
        $productTypes = ProductType::query()
            ->with('product:id,saler_id')
            ->select('id', 'count', 'price', 'product_id', 'is_published')
            ->find(array_keys($cart));

        foreach ($cart as $productType_id => $amount) {
            $productType = $productTypes->where('id', $productType_id)->first();

            $update[$productType_id]['id'] = $productType->id;
            $update[$productType_id]['count'] = $productType->count - $amount;
            $update[$productType_id]['is_published'] = $update[$productType_id]['count'] > 0 ? $productType->is_published : 0;

            $cart[$productType_id] = [
                'productType_id' => $productType_id,
                'amount' => $amount,
                'price' => $productType->price * $amount,
                'saler_id' => $productType->product->saler_id,
            ];
        }
        if (!empty($update)) ProductType::query()->upsert($update, 'id');
        return $this;
    }

    /**
     * @param Order $order
     * @return void
     */
    public function completeStore(Order $order, string $payment_id): void
    {
        $orderPerformers = collect($order->productTypes)
            ->groupBy('saler_id')/** @phpstan-ignore-next-line */
            ->transform(function (Collection $orderPerformer, int $saler_id) use ($order) {
                return [
                    'order_id' => $order->id,
                    'saler_id' => $saler_id,
                    'user_id' => $order->user_id,
                    'productTypes' => $orderPerformer,
                    'dispatch_time' => now()->addDays(25),
                    'delivery' => $order->delivery,
                    'total_price' => $orderPerformer->sum('price'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });
        DB::beginTransaction();
        $order->update(['status' => 'В работе', 'payment_id' => $payment_id]);
        OrderPerformer::query()->insert($orderPerformers->values()->all());
        event(new OrderStored($order));
        DB::commit();
    }

    public function update(Order $order): void
    {
        DB::beginTransaction();
        $order->update(['status' => 'Получен ' . now()]);
        $order->orderPerformers()->update(['status' => 'Получен ' . now()]);
        DB::commit();
    }

    public function delete(Order $order, bool $due_to_payment = false): void
    {
        $now = now();
        $type_upd = [];
        $productTypes = array_column((array)$order->productTypes, 'amount', 'productType_id');
        ProductType::query()
            ->whereIn('id', array_keys($productTypes))
            ->pluck('count', 'id')
            ->each(function (int $count, int $id) use (&$type_upd, $productTypes) {
                $type_upd[] = ['id' => $id, 'count' => $count + $productTypes[$id]];
            });

        DB::beginTransaction();
        ProductType::upsert($type_upd, 'id', ['count']);
        $order->update(['status' => 'Отменен ' . now(), 'deleted_at' => $now]);
        if (!$due_to_payment) {
            $order->orderPerformers()->update(['status' => 'Отменен ' . $now, 'deleted_at' => $now]);
            event(new OrderCanceled($order));
        }
        DB::commit();
    }
}
