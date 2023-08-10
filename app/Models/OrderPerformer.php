<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderPerformer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order_performers';
    protected $guarded = false;
    protected $casts = ['productTypes' => 'array'];

    public function saler()
    {
        return $this->beLongsTo(User::class, 'saler_id', 'id');
    }

    public function user()
    {
        return $this->beLongsTo(User::class, 'user_id', 'id');
    }

    public function order()
    {
        return $this->beLongsTo(Order::class, 'order_id', 'id');
    }
}
