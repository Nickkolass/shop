<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPerformer extends Model
{
    use HasFactory;
    protected $table = 'order_performers';
    protected $guarded = false;


    public function saler()
    {
        return $this->beLongsTo(User::class, 'saler_id', 'id');
    }

    public function user()
    {
        return $this->beLongsTo(User::class, 'user_id', 'id');
    }
}
