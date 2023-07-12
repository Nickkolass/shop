<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements MustVerifyEmail, JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    protected $guarded = false;

    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

    const ROLE_ADMIN = 1;
    const ROLE_SALER = 2;
    const ROLE_CLIENT = 3;


    public static function getRoles()
    {
        return [
            self::ROLE_ADMIN => 'admin',
            self::ROLE_SALER => 'saler',
            self::ROLE_CLIENT => 'client',
        ];
    }

    public function getRoleTitleAttribute()
    {
        return self::getRoles()[$this->role];
    }

    public function isAdmin()
    {
        return self::getRoles()[$this->role] == 'admin';
    }

    public function isSaler()
    {
        return self::getRoles()[$this->role] == 'saler';
    }

    static function getGenders()
    {
        return [
            self::GENDER_MALE => 'Мужской',
            self::GENDER_FEMALE => 'Женский',
        ];
    }

    public function getGenderTitleAttribute()
    {
        return self::getGenders()[$this->gender];
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'saler_id', 'id');
    }

    public function productTypes()
    {
        return $this->hasManyThrough(ProductType::class, Product::class, 'saler_id', 'product_id', 'id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    public function orderPerformers()
    {
        return $this->hasMany(OrderPerformer::class, 'saler_id', 'id');
    }

    public function liked()
    {
        return $this->beLongsToMany(ProductType::class, 'productType_user_likes', 'user_id','productType_id');
    }

    public function ratingAndComments()
    {
        return $this->hasMany(RatingAndComment::class, 'user_id', 'id');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'surname',
        'patronymic',
        'age',
        'address',
        'gender',
        'card',
        'postcode',
        'address',
        'INN',
        'registredOffice',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
