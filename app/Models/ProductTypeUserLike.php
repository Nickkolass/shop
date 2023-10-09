<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $productType_id
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ProductTypeUserLike extends Model
{

    protected $table = 'productType_user_likes';
    protected $guarded = false;
    protected $hidden = ['created_at', 'updated_at', 'pivot'];

    public function productType(): BelongsTo
    {
        return $this->beLongsTo(ProductType::class, 'productType_id', 'id');
    }
}
