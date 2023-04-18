<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyValue extends Model
{
    use HasFactory;
    
    protected $table = 'property_values';
    protected $guarded = false;
    protected $hidden = array('pivot');


    public function property()
    {
        return $this->beLongsTo(Property::class, 'property_id', 'id');
    }

    public function products()
    {
        return $this->beLongsToMany(Product::class, 'property_value_products', 'property_value_id', 'product_id');
    }
}
