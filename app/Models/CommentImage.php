<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentImage extends Model
{
    use HasFactory;

    protected $table = 'comment_images';
    protected $guarded = false;

    public function comment()
    {
        return $this->belongsTo(RatingAndComment::class, 'id', 'comment_id');
    }
}
