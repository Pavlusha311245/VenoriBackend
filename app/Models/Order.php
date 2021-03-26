<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'price',
        'datetime',
        'people',
        'special',
        'staying',
        'user_id',
        'place_id',
    ];

    public function place(){
        return $this->hasOne(Place::class);
    }

    public function user(){
        return $this->hasOne(User::class);
    }
}
