<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{

    use HasFactory;

    protected $table = 'colors';
    protected $guarded = false;
    protected $hidden = array('pivot');
    
    public function products(){
        return $this->beLongsToMany(Product::class, 'color_products', 'color_id','product_id');
    }

}
