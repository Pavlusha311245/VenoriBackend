<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
