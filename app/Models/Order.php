<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{

    use SoftDeletes, HasFactory;

    protected $table = 'orders';
    protected $guarded = false;


    public function user()
    {
        return $this->beLongsTo(User::class, 'user_id', 'id');
    }

    public function orderPerformers()
    {
        return $this->hasMany(OrderPerformer::class, 'order_id', 'id');
    }
 }
