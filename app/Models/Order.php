<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @OA\Schema(
 * required={"status","price","date","people","staying","time","staying_end","user_id","place_id"},
 * @OA\Xml(name="Order"),
 * @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 * @OA\Property(property="status", type="enum", example={"In Progress","Rejected","Confirmed"}),
 * @OA\Property(property="price", type="decimal", example="20.99"),
 * @OA\Property(property="staying", type="integer", example="2"),
 * @OA\Property(property="time", type="time", example="17:30"),
 * @OA\Property(property="staying_end", type="time", example="19:30"),
 * @OA\Property(property="user_id", type="integer", readOnly="true", example="1"),
 * @OA\Property(property="place_id", type="integer", readOnly="true", example="1"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Initial creation timestamp", readOnly="true"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", readOnly="true")
 * )
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
        return $this->hasOne(Place::class);
    }

    /**
     * Relationship with User
     * @return HasOne
     */
    public function user(){
        return $this->hasOne(User::class);
    }
}
