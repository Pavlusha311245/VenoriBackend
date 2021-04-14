<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @OA\Schema(
 * required={"status","price", "date", "people", "staying", "time", "staying_end", "user_id","place_id"},
 * @OA\Xml(name="Order"),
 * @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 * @OA\Property(property="status", type="enum", example="Confirmed");
 * @OA\Property(property="second_name", type="string", maxLength=255, example="Doe"),
 * @OA\Property(property="email", type="string", format="email", description="User unique email address", example="user@gmail.com"),
 * @OA\Property(property="address_full", type="string", maxLength=255, example="John"),
 * @OA\Property(property="address_lat", type="double", example="34.22"),
 * @OA\Property(property="address_lon", type="double", example="34.32"),
 * @OA\Property(property="email_verified_at", type="string", readOnly="true", format="date-time", description="Datetime marker of verification status", example="2019-02-25 12:59:20"),
 * @OA\Property(property="password", type="string", maxLength=255, example="Passwo424hg"),
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
