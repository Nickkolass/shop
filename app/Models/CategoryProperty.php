<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $category_id
 * @property int $property_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class CategoryProperty extends Model
{

    protected $table = 'category_properties';
    protected $guarded = false;
    protected $hidden = ['created_at', 'updated_at', 'pivot'];
}
