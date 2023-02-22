<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $guarded = false;


    public function user()
    {
        return $this->beLongsTo(User::class, 'user_id', 'id');
    }

    public function orderPerformers()
    {
        return $this->hasMany(OrderPerformer::class, 'user_id', 'id');
    }
 }
