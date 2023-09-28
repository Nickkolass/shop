<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $title
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property ?Collection<int, PropertyValue> $propertyValues
 * @property ?Collection<int, Category> $categories
 */
class Property extends Model
{
    use HasFactory;

    protected $table = 'properties';
    protected $guarded = false;
    protected $hidden = ['created_at', 'updated_at'];

    public function propertyValues(): HasMany
    {
        return $this->hasMany(PropertyValue::class, 'property_id', 'id');
    }

    public function categories(): BelongsToMany
    {
        return $this->beLongsToMany(Category::class, 'category_properties', 'property_id', 'category_id');
    }
}

