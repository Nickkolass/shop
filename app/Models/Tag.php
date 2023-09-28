<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $title
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property ?Collection<int, Product> $products
 */
class Tag extends Model
{

    use HasFactory;

    protected $table = 'tags';
    protected $guarded = false;
    protected $hidden = ['created_at', 'updated_at', 'pivot'];

    public function products(): BelongsToMany
    {
        return $this->beLongsToMany(Product::class, 'product_tags', 'tag_id', 'product_id');
    }
}
