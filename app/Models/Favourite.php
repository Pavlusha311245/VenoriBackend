<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function user(){
        return $this->hasOne(User::class);
    }

    public function place(){
        return $this->hasOne(Place::class);
    }
}
