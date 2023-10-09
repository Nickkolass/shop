<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $optionValue_id
 * @property int $productType_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ProductTypeOptionValue extends Model
{
    protected $table = 'productType_optionValues';
    protected $guarded = false;
    protected $hidden = ['created_at', 'updated_at', 'pivot'];
}
