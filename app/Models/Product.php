<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 * @OA\Schema(
 *      @OA\Xml(name="Product"),
 *      @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *      @OA\Property(property="name", type="string", description="Product name", example="Americano"),
 *      @OA\Property(property="weight", type="string", description="Product weight", example="200ml"),
 *      @OA\Property(property="price", type="number", description="Product price", example=4.00),
 *      @OA\Property(property="image_url", type="string", description="On creation and update, accepts a file, but stores and returns a link to the file on the server", example="storage/products/coffe_americano.png"),
 *      @OA\Property(property="category_id", type="integer", description="ID of the category the product belongs to", example=1),
 *      @OA\Property(property="created_at", type="string", format="date-time", description="Initial creation timestamp", readOnly=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", readOnly=true)
 * )
 *
 * Class Product
 *
 */
class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'weight',
        'price',
        'category_id',
        'image_url'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function places()
    {
        return $this->belongsToMany(Place::class, 'products_of_places');
    }
}
