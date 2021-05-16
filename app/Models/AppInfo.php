<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

/**
 *
 * @OA\Schema(
 *      @OA\Xml(name="AppInfo"),
 *      @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *      @OA\Property(property="about", type="string", description="About application", example="Very larg text..."),
 *      @OA\Property(property="contact", type="string", description="Application contacts (Phones, Addresses)", example="Very larg text..."),
 *      @OA\Property(property="terms", type="string", description="Terms", example="Very larg text..."),
 *      @OA\Property(property="privacy_policy", type="string", description="Privacy policy", example="Very larg text..."),
 *      @OA\Property(property="created_at", type="string", format="date-time", description="Initial creation timestamp", readOnly=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", readOnly=true)
 * )
 *
 * Class AppInfo
 *
 */
class AppInfo extends Model
{
    use HasFactory, QueryCacheable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'about',
        'contact',
        'terms',
        'privacy_policy'
    ];
}
