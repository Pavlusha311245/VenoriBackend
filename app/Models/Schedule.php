<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'place_id',
        'work_start',
        'work_end',
        'lunch_start',
        'lunch_end'
    ];
}
