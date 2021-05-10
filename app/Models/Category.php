<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rennokki\QueryCache\Traits\QueryCacheable;

/**
 *
 * @OA\Schema(
 *      @OA\Xml(name="Category"),
 *      @OA\Property(property="id", type="integer", readOnly=true, example="1"),
 *      @OA\Property(property="name", type="string", description="Category name", example="Coffee"),
 *      @OA\Property(property="image_url", type="string", description="On creation and update, accepts a file, but stores and returns a link to the file on the server", example="storage/category/coffee.png", ),
 *      @OA\Property(property="created_at", type="string", format="date-time", description="Initial creation timestamp", readOnly=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", readOnly=true)
 * )
 *
 * Class Category
 *
 */
class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'image_url'
    ];

    /**
     * Relationship with Product
     *
     * @return HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    public function places()
    {
        return $this->belongsToMany(Place::class, 'categories_of_places');
    }
}
