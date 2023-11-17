<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $title
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property ?Collection<OptionValue> $optionValues
 */
class Option extends Model
{

    use HasFactory;

    protected $table = 'options';
    protected $guarded = false;
    protected $hidden = ['created_at', 'updated_at'];

    public function optionValues(): HasMany
    {
        return $this->hasMany(OptionValue::class, 'option_id', 'id');
    }
}
