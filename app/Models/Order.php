<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $user_id
 * @property array|Collection<int, ProductType> $productTypes
 * @property string $delivery
 * @property int $total_price
 * @property string $payment
 * @property bool $payment_status
 * @property string $status
 * @property ?Carbon $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property ?User $user
 * @property ?Collection<int, OrderPerformer> $orderPerformers
 */
class Order extends Model
{

    use SoftDeletes, HasFactory;

    protected $table = 'orders';
    protected $guarded = false;
    protected $casts = ['productTypes' => 'array'];

    public function user(): BelongsTo
    {
        return $this->beLongsTo(User::class, 'user_id', 'id');
    }

    public function orderPerformers(): HasMany
    {
        return $this->hasMany(OrderPerformer::class, 'order_id', 'id');
    }
}
