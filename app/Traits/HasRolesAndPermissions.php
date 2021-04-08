<?php
namespace App\Traits;

use App\Models\Role;
use App\Models\Permission;

/**
 * Trait HasRolesAndPermissions
 * @package App\Traits
 */
trait HasRolesAndPermissions
{
    /**
     * Roles Trait
     *
     * @return mixed
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class,'users_roles');
    }

    /**
     * Permissions Trait
     *
     * @return mixed
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class,'users_permissions');
    }

    /**
     * Check on Role of User
     *
     * @param mixed ...$roles
     * @return bool
     */
    public function hasRole(... $roles)
    {
        foreach ($roles as $role) {
            if ($this->roles->contains('slug', $role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check on Permissions of User
     *
     * @param $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        return (bool) $this->permissions->where('slug', $permission)->count();
    }
    /**
     * Check on Permissions
     * @param $permission
     * @return bool
     */
    public function hasPermissionTo($permission)
    {
        return $this->hasPermissionThroughRole($permission) || $this->hasPermission($permission->slug);
    }

    /**
     * Has User role and permission
     * @param $permission
     * @return bool
     */
    public function hasPermissionThroughRole($permission)
    {
        foreach ($permission->roles as $role){
            if($this->roles->contains($role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get All Permissions
     *
     * @param array $permissions
     * @return mixed
     */
    public function getAllPermissions(array $permissions)
    {
        return Permission::whereIn('slug',$permissions)->get();
    }

    /**
     * Give Permissions to User
     *
     * @param mixed ...$permissions
     * @return $this
     */
    public function givePermissionsTo(... $permissions)
    {
        $permissions = $this->getAllPermissions($permissions);
        if($permissions === null) {
            return $this;
        }
        $this->permissions()->saveMany($permissions);
        return $this;
    }

    /**
     * Delete Permissions
     *
     * @param mixed ...$permissions
     * @return $this
     */
    public function deletePermissions(... $permissions )
    {
        $permissions = $this->getAllPermissions($permissions);
        $this->permissions()->detach($permissions);
        return $this;
    }
    /**
     * Refresh Permissions
     *
     * @param mixed ...$permissions
     * @return HasRolesAndPermissions
     */
    public function refreshPermissions(... $permissions )
    {
        $this->permissions()->detach();
        return $this->givePermissionsTo($permissions);
    }
}
