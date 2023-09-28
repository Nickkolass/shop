<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $product_id
 * @property int $tag_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ProductTag extends Model
{
    protected $table = 'product_tags';
    protected $guarded = false;
    protected $hidden = ['created_at', 'updated_at', 'pivot'];
}
