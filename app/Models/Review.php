<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'rating',
        'description',
        'like',
        'place_id',
        'user_id'
    ];

    /**
     * Relationship with Place
     *
     * @return HasMany
     */
    public function places(){
        return $this->hasMany(Place::class);
    }

    /**
     * Relationship with Place
     *
     * @return HasMany
     */
    public function comments(){
        return $this->hasMany(Comment::class);
    }
}
