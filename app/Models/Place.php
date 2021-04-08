<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Place extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'rating',
        'phone',
        'review_id',
        'capacity',
        'description',
        'address_full',
        'address_lat',
        'address_lon'
    ];

    /**
     * Relationship with Order
     *
     * @return HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relationship with Favourite
     *
     * @return HasMany
     */
    public function favourites()
    {
        return $this->hasMany(Favourite::class);
    }

    /**
     * Relationship with productsOfPlace
     *
     * @return HasMany
     */
    public function productsOfPlace()
    {
        return $this->hasMany(ProductsOfPlace::class);
    }

    /**
     * Relationship with Review
     *
     * @return HasOne
     */
    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
