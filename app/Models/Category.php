<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    protected $table = 'categories';
    protected $guarded = false;

    public function groups(){
        return $this->hasMany(Group::class, 'category_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    public function properties()
    {
        return $this->beLongsToMany(Property::class, 'category_properties', 'category_id', 'property_id');
    }
}
