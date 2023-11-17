<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $user_id
 * @property int $saler_id
 * @property int $order_id
 * @property Carbon $dispatch_time
 * @property int $status
 * @property array<array{productType_id:int, saler_id:int, amount:int, price:int}>|Collection<ProductType> $productTypes
 * @property string $delivery
 * @property int $total_price
 * @property ?string $payout_id
 * @property ?Carbon $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property ?User $saler
 * @property ?User $user
 * @property ?Order $order
 */
class OrderPerformer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order_performers';
    protected $guarded = false;
    protected $casts = [
        'productTypes' => 'array',
        'dispatch_time' => 'datetime',
    ];


    const STATUS_WAIT_PAYMENT = 0;
    const STATUS_WAIT_DELIVERY = 1;
    const STATUS_SENT = 2;
    const STATUS_RECEIVED = 3;
    const STATUS_PAYOUT = 4;
    const STATUS_CANCELED = 5;

    /**
     * @return array<int, string>
     */
    public static function getStatuses(): array
    {
        return ['Ожидает оплаты', 'Ожидает отправки', 'Отправлен', 'Получен', 'Завершен', 'Отменен'];
    }

    public function getStatusTitleAttribute(): string
    {
        return self::getStatuses()[$this->status];
    }

    public function order(): BelongsTo
    {
        return $this->beLongsTo(Order::class, 'order_id', 'id');
    }

    public function saler(): BelongsTo
    {
        return $this->beLongsTo(User::class, 'saler_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->beLongsTo(User::class, 'user_id', 'id');
    }
}
