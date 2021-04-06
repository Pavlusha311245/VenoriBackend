<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Favourite extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'place_id',
    ];

    /**
     * Relationship with User
     * @return HasOne
     */
    public function user(){
        return $this->hasOne(User::class);
    }

    /**
     * Relationship with Place
     * @return HasOne
     */
    public function place(){
        return $this->hasOne(Place::class);
    }
}
