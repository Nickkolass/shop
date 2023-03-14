<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wick extends Model
{
    use HasFactory;

    protected $table = 'wicks';
    protected $guarded = false;

    public function candles()
    {
        return $this->hasMany(Candle::class, 'wick_id', 'id');
    }

}
