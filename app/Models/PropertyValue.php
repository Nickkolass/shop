<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int id
 * @property int property_id
 * @property string value
 * @property Carbon created_at
 * @property Carbon updated_at
 */

class PropertyValue extends Model
{
    use HasFactory;

    protected $table = 'property_values';
    protected $guarded = false;
    protected $hidden = ['created_at', 'updated_at', 'pivot'];

    public function property(): BelongsTo
    {
        return $this->beLongsTo(Property::class, 'property_id', 'id');
    }

    public function products(): BelongsToMany
    {
        return $this->beLongsToMany(Product::class, 'property_value_products', 'property_value_id', 'product_id');
    }
}
