<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingAndComment extends Model
{
    use HasFactory;

    protected $table = 'rating_and_comments';
    protected $guarded = false;

    public function product()
    {
        return $this->belongsTo(Product::class, 'id', 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function commentImages()
    {
        return $this->hasMany(CommentImage::class, 'comment_id', 'id');
    }
}
