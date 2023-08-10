<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptionValueProduct extends Model
{
    use HasFactory;
    protected $table = 'optionValue_products';
    protected $guarded = false;

    public function optionValues()
    {
        return $this->beLongsTo(OptionValue::class, 'optionValue_id', 'id');
    }
}
