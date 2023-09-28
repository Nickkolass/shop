<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $product_id
 * @property int $user_id
 * @property ?string $message
 * @property int $rating
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property ?User $user
 * @property ?Product $product
 * @property ?Collection<int, CommentImage> $commentImages
 */
class RatingAndComment extends Model
{
    use HasFactory;

    protected $table = 'rating_and_comments';
    protected $guarded = false;

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'id', 'product_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function commentImages(): HasMany
    {
        return $this->hasMany(CommentImage::class, 'comment_id', 'id');
    }
}
