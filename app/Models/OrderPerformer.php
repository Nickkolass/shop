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
 * @property string $status
 * @property array|Collection<int, ProductType> $productTypes
 * @property string $delivery
 * @property int $total_price
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
    protected $casts = ['productTypes' => 'array'];

    public function saler(): BelongsTo
    {
        return $this->beLongsTo(User::class, 'saler_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->beLongsTo(User::class, 'user_id', 'id');
    }

    public function order(): BelongsTo
    {
        return $this->beLongsTo(Order::class, 'order_id', 'id');
    }
}
