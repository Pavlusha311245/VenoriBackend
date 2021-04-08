<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Role
 *
 * @package App\Models
 */
class Role extends Model
{
    /**
     * Role with Permissions Relationship
     *
     * @return BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class,'roles_permissions');
    }
}
