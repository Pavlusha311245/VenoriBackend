<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 *
 * @OA\Schema(
 * @OA\Xml(name="Comment"),
 * @OA\Property(property="id", type="integer", readOnly=true, example=1),
 * @OA\Property(property="title", type="string", description="Comment title", example="It lie!!"),
 * @OA\Property(property="description", type="string", description="The main text of the comment", example="All food in this place was bad! -_-"),
 * @OA\Property(property="review_id", type="integer", description="ID of the review to which the comment applies", example=1),
 * @OA\Property(property="user_id", type="integer", description="user ID who left the comment", example=1),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Initial creation timestamp", readOnly=true),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", readOnly=true),
 * )
 *
 * Class Comment
 *
 */
class Comment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'review_id',
        'user_id'
    ];

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
