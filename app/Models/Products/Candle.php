<?php

namespace App\Models\Products;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candle extends Model
{
    use HasFactory;

    protected $table = 'candles';
    protected $guarded = false;

    public function products()
    {
        return $this->hasMany(Product::class, 'product_id', 'id');
    }

    public function Wick()
    {
        return $this->belongsTo(Wick::class, 'wick_id', 'id');
    }
    

}
