<?php

namespace App\Services\Client\API\Order;

use App\Events\Order\OrderCanceled;
use App\Events\Order\OrderReceived;
use App\Jobs\Client\Order\OrderStoredJob;
use App\Models\Order;
use App\Models\OrderPerformer;
use App\Models\ProductType;
use App\Models\User;
use App\Services\Payment\PaymentService;
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
        $this
            ->countProductReduction($data['cart'])
            ->orderStore($data)
            ->orderPerformerStore($data['order']);
        $url = app(PaymentService::class)->payment($data['order']);
        dispatch(new OrderStoredJob($data['order']))->delay(600);
        DB::commit();
        return $url;
    }

    /**
     * @param array<int> &$cart
     * @return self
     */
    private function countProductReduction(array &$cart): self
    {
        $productTypes = ProductType::query()
            ->with('product:id,saler_id')
            ->select('id', 'count', 'price', 'product_id', 'is_published')
            ->find(array_keys($cart));

        foreach ($cart as $productType_id => $amount) {
            $productType = $productTypes->firstWhere('id', $productType_id);

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
     * @param array<mixed> &$data
     * @return self
     */
    private function orderStore(array &$data): self
    {
        /** @var User $user */
        $user = auth('api')->user();

        $data['order'] = Order::query()->create([
            'user_id' => $user->id,
            'productTypes' => array_values($data['cart']),
            'delivery' => $data['delivery'] . ". Получатель: $user->surname $user->name $user->patronymic. Адрес: $user->address",
            'total_price' => $data['total_price'],
        ]);
        return $this;
    }

    /**
     * @param Order $order
     * @return self
     */
    private function orderPerformerStore(Order $order): self
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
            })
            ->values()
            ->all();
        OrderPerformer::query()->insert($orderPerformers);
        return $this;
    }

    public function update(Order $order): void
    {
        DB::beginTransaction();
        $order->increment('status');
        $order->orderPerformers()->update(['status' => OrderPerformer::STATUS_RECEIVED]);
        event(new OrderReceived($order));
        DB::commit();
    }

    public function delete(Order $order, bool $due_to_payment): void
    {
        $order->load('orderPerformers:id,order_id,productTypes,status');
        DB::beginTransaction();
        $orderPerformer_deleted_ids = $this->countProductRestoration($order)->orderDelete($order, $due_to_payment);
        if (!$due_to_payment) event(new OrderCanceled($order, $orderPerformer_deleted_ids));
        DB::commit();
    }

    /**
     * @param Order $order
     * @return self
     */
    private function countProductRestoration(Order $order): self
    {
        $productTypes = $order
            ->orderPerformers
            ->where('status', OrderPerformer::STATUS_WAIT_DELIVERY)
            ->pluck('productTypes')
            ->flatten(1)
            ->pluck('amount', 'productType_id');

        $type_upd = ProductType::query()
            ->whereIn('id', $productTypes->keys())
            ->pluck('count', 'id')
            ->transform(function (int $count, int $id) use ($productTypes) {
                return ['id' => $id, 'count' => $count + $productTypes[$id], 'is_published' => true];
            })
            ->all();
        ProductType::upsert($type_upd, 'id');
        return $this;
    }

    /**
     * @param Order $order
     * @param bool $due_to_payment
     * @return null|array<int> $orderPerformer_deleted_ids
     */
    private function orderDelete(Order $order, bool $due_to_payment): ?array
    {
        if ($due_to_payment) {
            $order->orderPerformers()->update(['status' => OrderPerformer::STATUS_CANCELED, 'deleted_at' => now()]);
            $order->update(['status' => Order::STATUS_CANCELED, 'deleted_at' => now()]);
            return null;
        } else {
            $delete_ids = $order
                ->orderPerformers
                ->where('status', OrderPerformer::STATUS_WAIT_DELIVERY)
                ->pluck('id')
                ->all();
            $order->orderPerformers()->whereIn('id', $delete_ids)->update(['status' => OrderPerformer::STATUS_CANCELED, 'deleted_at' => now()]);
            Order::query()
                ->take(1)
                ->where('id', $order->id)
                ->doesntHave('orderPerformers')
                ->update(['status' => Order::STATUS_CANCELED, 'deleted_at' => now()]);

            return $delete_ids;
        }
    }
}
