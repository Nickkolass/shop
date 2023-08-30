<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int id
 * @property int optionValue_id
 * @property int product_id
 * @property Carbon created_at
 * @property Carbon updated_at
 */

class OptionValueProduct extends Model
{
    use HasFactory;
    protected $table = 'optionValue_products';
    protected $guarded = false;
    protected $hidden = ['created_at', 'updated_at', 'pivot'];

    public function optionValues(): BelongsTo
    {
        return $this->beLongsTo(OptionValue::class, 'optionValue_id', 'id');
    }
}
