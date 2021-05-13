<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 * @OA\Schema(
 *      @OA\Xml(name="Schedule"),
 *      @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *      @OA\Property(property="place_id", type="integer", description="Id of the place to which the schedule belongs", example=1),
 *      @OA\Property(property="work_start", type="string", format="time", description="Start time", example="8:00"),
 *      @OA\Property(property="work_end", type="string", format="time", description="End time", example="19:00"),
 *      @OA\Property(property="created_at", type="string", format="date-time", description="Initial creation timestamp", readOnly=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", readOnly=true)
 * )
 *
 * Class Schedule
 *
 */
class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'place_id',
        'work_start',
        'work_end',
    ];
}
