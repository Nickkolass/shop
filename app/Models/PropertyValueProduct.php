<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $propertyValue_id
 * @property int $product_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class PropertyValueProduct extends Model
{
    use HasFactory;

    protected $table = 'property_value_products';
    protected $guarded = false;
    protected $hidden = ['created_at', 'updated_at', 'pivot'];
}
