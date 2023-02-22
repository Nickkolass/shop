<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

    protected $table = 'users';
    protected $guarded = false;

    static function getGenders(){
        return [
            self::GENDER_MALE => 'Мужской',
            self::GENDER_FEMALE => 'Женский',
        ];
    }

    public function getGenderTitleAttribute(){
        return self::getGenders()[$this->gender];
    }

    public function groups(){
        return $this->hasMany(Group::class, 'saler_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'saler_id', 'id');
    }

    public function productsThrough()
    {
        return $this->hasManyThrough(Product::class, Group::class);
    }

    public function categoriesThrough()
    {
        return $this->beLongsToMany(Category::class, 'products', 'saler_id', 'category_id');
    }

    public function orders(){
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    public function OrderPerformers(){
        return $this->hasMany(OrderPerformer::class, 'saler_id', 'id');
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

    
}
