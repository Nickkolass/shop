<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $productType_id
 * @property string $file_path
 * @property int $size
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property ?ProductType $productType
 */
class ProductImage extends Model
{
    use HasFactory;

    protected $table = 'product_images';
    protected $guarded = false;
    protected $hidden = ['created_at', 'updated_at'];

    public function productType(): BelongsTo
    {
        return $this->beLongsTo(ProductType::class, 'productType_id', 'id');
    }
}
