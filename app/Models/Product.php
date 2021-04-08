<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        return $this->hasOne(Category::class);
    }

    public function productsOfPlace()
    {
        return $this->hasMany(ProductsOfPlace::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
