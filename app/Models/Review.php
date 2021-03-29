<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    ];

    public function places(){
        return $this->hasMany(Place::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }
}
