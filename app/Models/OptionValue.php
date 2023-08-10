<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptionValue extends Model
{
    use HasFactory;
    protected $table = 'optionValues';
    protected $guarded = false;
    protected $hidden = array('pivot');

    public function option()
    {
        return $this->beLongsTo(Option::class, 'option_id', 'id');
    }

    public function products()
    {
        return $this->beLongsToMany(Product::class, 'optionValue_products', 'optionValue_id', 'product_id');
    }

    public function productTypes()
    {
        return $this->beLongsToMany(ProductType::class, 'productType_optionValues', 'optionValue_id', 'productType_id');
    }
}
