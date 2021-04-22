<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 *
 * @OA\Schema(
 *      @OA\Xml(name="Favourite"),
 *      @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *      @OA\Property(property="user_id", type="integer", description="user ID", example=1),
 *      @OA\Property(property="place_id", type="integer", description="go to the place that the user likes", example=1),
 *      @OA\Property(property="created_at", type="string", format="date-time", description="Initial creation timestamp", readOnly=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", readOnly=true)
 * )
 *
 * Class Favourite
 *
 */
class Favourite extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'place_id',
    ];

    /**
     * Relationship with User
     * @return HasOne
     */
    public function user(){
        return $this->hasOne(User::class);
    }

    /**
     * Relationship with Place
     * @return HasOne
     */
    public function place(){
        return $this->hasOne(Place::class);
    }
}
