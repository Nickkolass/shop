<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int id
 * @property int comment_id
 * @property string file_path
 * @property int size
 * @property Carbon created_at
 * @property Carbon updated_at
 */

class CommentImage extends Model
{
    use HasFactory;

    protected $table = 'comment_images';
    protected $guarded = false;
    protected $hidden = ['created_at', 'updated_at'];

    public function comment(): BelongsTo
    {
        return $this->belongsTo(RatingAndComment::class, 'id', 'comment_id');
    }
}
