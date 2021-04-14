<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
