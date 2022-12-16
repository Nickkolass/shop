<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    use Filterable;

    protected $table = 'products';
    protected $guarded = false;

    public function category(){
        return $this->beLongsTo(Category::class, 'category_id', 'id');
    }

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

}
