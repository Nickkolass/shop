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

    public function productsThrough()
    {
        return $this->hasManyThrough(Product::class, Group::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    public function salersThrough()
    {
        return $this->beLongsToMany(User::class, 'products', 'saler_id', 'category_id');
    }
}
