<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @OA\Schema(
 *      @OA\Xml(name="Order"),
 *      @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *      @OA\Property(property="status", type="string", example={"In Progress","Rejected","Confirmed"}),
 *      @OA\Property(property="price", type="number", example=2000),
 *      @OA\Property(property="date", type="string", format="date", example="2021-04-21"),
 *      @OA\Property(property="people", type="number", example=10),
 *      @OA\Property(property="staying", type="integer", example=2),
 *      @OA\Property(property="time", type="string", format="time", example="17:00"),
 *      @OA\Property(property="staying_end", type="string", format="time", example="19:00"),
 *      @OA\Property(property="user_id", type="integer", readOnly=true, example=1),
 *      @OA\Property(property="place_id", type="integer", readOnly=true, example=1),
 *      @OA\Property(property="created_at", type="string", format="date-time", description="Initial creation timestamp", readOnly=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", readOnly=true)
 * )
 *
 * Class Order
 *
 */
class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'price',
        'date',
        'people',
        'time',
        'staying',
        'staying_end',
        'user_id',
        'place_id',
    ];

    /**
     * Relationship with Place
     * @return HasOne
     */
    public function place(){
        return $this->belongsTo(Place::class);
    }

    /**
     * Relationship with User
     * @return HasOne
     */
    public function user(){
        return $this->belongsTo(User::class);
    }
}
