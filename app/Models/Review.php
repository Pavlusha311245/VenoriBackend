<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 * @OA\Schema(
 *      @OA\Xml(name="Review"),
 *      @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *      @OA\Property(property="title", type="string", description="Review title", example="Gread place!!!"),
 *      @OA\Property(property="rating", type="number", description="User rating", example=5),
 *      @OA\Property(property="description", type="string", description="The main text of the review", example="Very cool restaurant, I liked everything"),
 *      @OA\Property(property="like", type="integer", description="The number of likes under the review from other users", example=1, readOnly=true),
 *      @OA\Property(property="user_id", type="integer", description="User ID of the user who left the review", example=1),
 *      @OA\Property(property="place_id", type="integer", description="Id of the place for which the review is left", example=1),
 *      @OA\Property(property="created_at", type="string", format="date-time", description="Initial creation timestamp", readOnly=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", readOnly=true)
 * )
 *
 * Class Review
 *
 */
class Review extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'rating',
        'description',
        'like',
        'place_id',
        'user_id'
    ];

    /**
     * Relationship with Place
     *
     * @return HasMany
     */
    public function places(){
        return $this->hasMany(Place::class);
    }

    /**
     * Relationship with Place
     *
     * @return HasMany
     */
    public function comments(){
        return $this->hasMany(Comment::class);
    }
}
