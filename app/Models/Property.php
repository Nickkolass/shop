<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $table = 'properties';
    protected $guarded = false;

    
    public function propertyValues()
    {
        return $this->hasMany(PropertyValue::class, 'property_id', 'id');
    }

    public function categories()
    {
        return $this->beLongsToMany(Category::class, 'category_properties', 'property_id', 'category_id');
    }
}  

