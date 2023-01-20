<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    
    use HasFactory;

    protected $table = 'tags';
    protected $guarded = false;

    public function products(){
        return $this->beLongsToMany(Product::class, 'product_tags', 'tag_id','product_id');
    }

}
