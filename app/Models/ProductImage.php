<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    use HasFactory;

    protected $table = 'product_images';
    protected $guarded = false;

    public function products(){
        return $this->beLongsTo(Product::class, 'product_id', 'id');
    }
       
}
