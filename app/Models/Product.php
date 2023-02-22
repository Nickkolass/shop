<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    use Filterable;
    use HasFactory;

    protected $table = 'products';
    protected $guarded = false;
    protected $hidden = array('pivot');



    
    public function colors(){
        return $this->beLongsToMany(Color::class, 'color_products', 'product_id', 'color_id');
    }

    public function tags(){
        return $this->beLongsToMany(Tag::class, 'product_tags', 'product_id', 'tag_id');
    }

    public function productImages(){
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }

    public function group(){
        return $this->beLongsTo(Group::class, 'group_id', 'id');
    }

    public function saler()
    {
        return $this->beLongsTo(User::class, 'saler_id', 'id');
    }

    public function salerThrough()
    {
        return $this->hasOneThrough(User::class, Group::class);
    }
    
    public function categoryThrough()
    {
        return $this->hasOneThrough(Category::class, Group::class);
    }

    public function category()
    {
        return $this->beLongsTo(Category::class, 'category_id', 'id');
    }
}
