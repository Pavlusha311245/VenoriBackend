<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @OA\Schema(
 * required={"name","type","phone","capacity","table_price","description","image_url"},
 * @OA\Xml(name="Place"),
 * @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", example="KFC"),
 * @OA\Property(property="image_url", type="string", example="storage\PlaceImages\KFC.png"),
 * @OA\Property(property="rating", type="double", example="4.23"),
 * @OA\Property(property="address_full", type="string", maxLength=255, example="John"),
 * @OA\Property(property="address_lat", type="double", example="34.22"),
 * @OA\Property(property="address_lon", type="double", example="34.32"),
 * @OA\Property(property="phone", type="string", maxLength=255, example="+375448675643"),
 * @OA\Property(property="description", type="string", example="KFC (short for Kentucky Fried Chicken) is an American fast food restaurant."),
 * @OA\Property(property="capacity", type="integer", example="450"),
 * @OA\Property(property="table_price", type="decimal", example="44.00"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Initial creation timestamp", readOnly="true"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", readOnly="true")
 * )
 */
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
        'table_price',
        'description',
        'address_full',
        'address_lat',
        'address_lon',
        'image_url'
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
