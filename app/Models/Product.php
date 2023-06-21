<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Laravel\Scout\Attributes\SearchUsingFullText;
use Laravel\Scout\Attributes\SearchUsingPrefix;

class Product extends Model
{

    use HasFactory, Searchable;

    protected $table = 'products';
    protected $guarded = false;
    protected $hidden = array('pivot');


    public function productTypes()
    {
        return $this->hasMany(ProductType::class, 'product_id', 'id');
    }

    public function ratingAndComments()
    {
        return $this->hasMany(RatingAndComments::class, 'product_id', 'id');
    }

    public function tags()
    {
        return $this->beLongsToMany(Tag::class, 'product_tags', 'product_id', 'tag_id');
    }

        public function saler()
    {
        return $this->beLongsTo(User::class, 'saler_id', 'id');
    }

    public function category()
    {
        return $this->beLongsTo(Category::class, 'category_id', 'id');
    }

    public function propertyValues()
    {
        return $this->beLongsToMany(PropertyValue::class, 'property_value_products', 'product_id', 'property_value_id');
    }

    public function optionValues()
    {
        return $this->beLongsToMany(OptionValue::class, 'optionValue_products', 'product_id', 'optionValue_id');
    }

    // #[SearchUsingPrefix(['id', 'email'])]
    #[SearchUsingFullText(['title', 'description'])]
    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
        ];
    }

}
