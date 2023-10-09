<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotificationQueue;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property int $id
 * @property int $role
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $surname
 * @property string $patronymic
 * @property int $age
 * @property int $gender
 * @property ?string $address
 * @property ?int $card
 * @property ?int $postcode
 * @property ?int $INN
 * @property ?string $registredOffice
 * @property ?string $remember_token
 * @property ?Carbon $email_verified_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property ?Collection<Product> $products
 * @property ?Collection<ProductType> $productTypes
 * @property ?Collection<Order> $orders
 * @property ?Collection<OrderPerformer> $orderPerformers
 * @property ?Collection<ProductType> $liked
 * @property ?Collection<RatingAndComment> $ratingAndComments
 */
class User extends Authenticatable implements MustVerifyEmail, JWTSubject
{
    use HasFactory, Notifiable;

    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;
    const ROLE_ADMIN = 1;
    const ROLE_SALER = 2;
    const ROLE_CLIENT = 3;
    protected $table = 'users';
    protected $guarded = false;
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['email_verified_at' => 'datetime'];
    protected $fillable = [
        'role', 'surname',
        'email', 'password',
        'name', 'patronymic',
        'age', 'address',
        'card', 'postcode',
        'gender', 'address',
        'INN', 'registredOffice',
    ];

    public function getRoleTitleAttribute(): string
    {
        return self::getRoles()[$this->role];
    }

    /**
     * @return array<string>
     */
    public static function getRoles(): array
    {
        return [
            self::ROLE_ADMIN => 'admin',
            self::ROLE_SALER => 'saler',
            self::ROLE_CLIENT => 'client',
        ];
    }

    public function isAdmin(): bool
    {
        return self::getRoles()[$this->role] == 'admin';
    }

    public function isSaler(): bool
    {
        return self::getRoles()[$this->role] == 'saler';
    }

    public function isClient(): bool
    {
        return self::getRoles()[$this->role] == 'client';
    }

    public function getGenderTitleAttribute(): string
    {
        return self::getGenders()[$this->gender];
    }

    /** @return array<string> */
    public static function getGenders(): array
    {
        return [
            self::GENDER_MALE => 'Мужской',
            self::GENDER_FEMALE => 'Женский',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'saler_id', 'id');
    }

    public function productTypes(): HasManyThrough
    {
        return $this->hasManyThrough(ProductType::class, Product::class, 'saler_id', 'product_id', 'id', 'id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    public function orderPerformers(): HasMany
    {
        return $this->hasMany(OrderPerformer::class, 'saler_id', 'id');
    }

    public function liked(): BelongsToMany
    {
        return $this->beLongsToMany(ProductType::class, 'productType_user_likes', 'user_id', 'productType_id');
    }

    public function ratingAndComments(): HasMany
    {
        return $this->hasMany(RatingAndComment::class, 'user_id', 'id');
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /** @return array{} */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotificationQueue($token));
    }
}
