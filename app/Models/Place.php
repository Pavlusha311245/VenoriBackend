<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'rating',
        'address_address',
        'address_latitude',
        'address_longitude',
        'phone',
        'work_start',
        'work_end',
        'review_id',
        'capacity',
        'description'
    ];

    public function orders(){
        return $this->hasMany(Order::class);
    }

    public function favourites(){
        return $this->hasMany(Favourite::class);
    }

    public function productsOfPlace(){
        return $this->hasMany(ProductsOfPlace::class);
    }

    public function review(){
        return $this->hasOne(Review::class);
    }
}
