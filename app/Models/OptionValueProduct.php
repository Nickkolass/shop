<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $optionValue_id
 * @property int $product_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property ?Collection<OptionValue> $optionValues
 */
class OptionValueProduct extends Model
{

    protected $table = 'optionValue_products';
    protected $guarded = false;
    protected $hidden = ['created_at', 'updated_at', 'pivot'];

    public function optionValues(): BelongsTo
    {
        return $this->beLongsTo(OptionValue::class, 'optionValue_id', 'id');
    }
}
