<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 * required={"product_id","place_id"},
 * @OA\Xml(name="ProductsOfPlace"),
 * @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 * @OA\Property(property="product_id", type="integer", readOnly="true", example="1"),
 * @OA\Property(property="place_id", type="integer", readOnly="true", example="1"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Initial creation timestamp", readOnly="true"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", readOnly="true")
 * )
 */
class ProductsOfPlace extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'place_id',
    ];

    public function product(){
        return $this->hasOne(Product::class);
    }

    public function place(){
        return $this->hasOne(Place::class);
    }
}
