<?php

namespace App\Models;

use App\Traits\HasRolesAndPermissions;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

/**
 * @OA\Schema(
 *      @OA\Xml(name="User"),
 *      @OA\Property(property="id", type="integer", readOnly=true, example="1"),
 *      @OA\Property(property="first_name", type="string", maxLength=255, example="John"),
 *      @OA\Property(property="second_name", type="string", maxLength=255, example="Doe"),
 *      @OA\Property(property="email", type="string", format="email", description="User unique email address", example="user@gmail.com"),
 *      @OA\Property(property="address_full", type="string", maxLength=255, example="Minsk"),
 *      @OA\Property(property="address_lat", type="number", example="53.913224"),
 *      @OA\Property(property="address_lon", type="number", example="27.467663"),
 *      @OA\Property(property="avatar", type="string", example="storage/UserImages/anton.png"),
 *      @OA\Property(property="email_verified_at", type="string", readOnly=true, format="date-time", description="Datetime marker of verification status", example="2019-02-25 12:59:20"),
 *      @OA\Property(property="created_at", type="string", format="date-time", description="Initial creation timestamp", readOnly=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", readOnly=true)
 * )
 *
 * Class User
 *
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, CanResetPassword, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'second_name',
        'email',
        'password',
        'avatar',
        'address_full',
        'address_lat',
        'address_lon'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function favoutirePlaces()
    {
        return $this->belongsToMany(Place::class, 'favourites');
    }

    public function managedPlaces() {
        return $this->belongsToMany(Place::class, 'places_managers','manager_id');
    }

    public function orders() {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
