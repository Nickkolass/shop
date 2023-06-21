<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTypeUserLike extends Model
{
    use HasFactory;

    protected $table = 'productType_user_likes';
    protected $guarded = false;

    public function productType()
    {
        return $this->beLongsTo(ProductType::class, 'productType_id', 'id');
    }
}
