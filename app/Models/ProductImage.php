<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $table = 'product_images';
    protected $guarded = false;

    public function productType(){
        return $this->beLongsTo(ProductType::class, 'productType_id', 'id');
    }

}
