<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int id
 * @property int option_id
 * @property string value
 * @property Carbon created_at
 * @property Carbon updated_at
 */

class OptionValue extends Model
{
    use HasFactory;
    protected $table = 'optionValues';
    protected $guarded = false;
    protected $hidden = ['created_at', 'updated_at', 'pivot'];

    public function option(): BelongsTo
    {
        return $this->beLongsTo(Option::class, 'option_id', 'id');
    }

    public function products(): BelongsToMany
    {
        return $this->beLongsToMany(Product::class, 'optionValue_products', 'optionValue_id', 'product_id');
    }

    public function productTypes(): BelongsToMany
    {
        return $this->beLongsToMany(ProductType::class, 'productType_optionValues', 'optionValue_id', 'productType_id');
    }
}
