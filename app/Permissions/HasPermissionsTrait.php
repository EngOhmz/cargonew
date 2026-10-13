<?php


namespace App\Permissions;

use App\Models\Permission;
use App\Models\Role;
trait HasPermissionsTrait {

 
    public function hasRole(...$roles)
    {
        foreach ($roles as $role) {
            if ($this->roles()->where('slug', $role)->exists()) {
                return true;
            }
        }
        return false;
    }

    public function giveRole(...$role)
    {
       // $roles = $this->getRoles(array_flatten($role));
        $roles = $this->getRoles(($role))->flatten();
        if (count($roles) < 1) {
            return false;
        }
        $this->roles()->saveMany($roles);
        return true;
    }

    public function getRoles(array $roles)
    {
        return $this->roles->whereIn('slug', $roles);
    }

    // Getting all

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'users_roles');
    }

    /**
     * Checks if User has access to $permissions.
     * @param $permission
     * @return bool
     */

    public function hasModuleTo($module)
    {
        if ($module == '' || $module == null) {
            return false;
        }
        if (!$this->roles()->first()->permissions->contains('modules.slug', $module->slug)) {
            return false;
        }
        return  true;
    }
    
   public function givePermissionsTo(... $permissions) {

    $permissions = $this->getAllPermissions($permissions);
    dd($permissions);
    if($permissions === null) {
      return $this;
    }
    $this->permissions()->saveMany($permissions);
    return $this;
  }

  public function withdrawPermissionsTo( ... $permissions ) {

    $permissions = $this->getAllPermissions($permissions);
    $this->permissions()->detach($permissions);
    return $this;

  }

  public function refreshPermissions( ... $permissions ) {

    $this->permissions()->detach();
    return $this->givePermissionsTo($permissions);
  }

    public function hasModuleAccess($moduleSlug, $permissionSlug = 'read')
    {
        if ($this->hasRole('superadmin', 'platform_admin')) {
            return true;
        }

        foreach ($this->roles as $role) {
            if ($role->hasModuleAccess($moduleSlug, $permissionSlug)) {
                return true;
            }
        }
        return false;
    }

    public function hasPermissionTo($permission) {
        if ($this->hasRole('superadmin', 'platform_admin')) {
            return true;
        }

        return $this->hasPermissionThroughRole($permission);
    }

  public function hasPermissionThroughRole($permission) {

    foreach ($permission->roles as $role){
      if($this->roles->contains($role)) {
        return true;
      }
    }
    return false;
  }

  public function permissions() {

    return $this->belongsToMany(Permission::class,'users_permissions');

  }
  protected function hasPermission($permission) {

    return (bool) $this->permissions->where('slug', $permission->slug)->count();
  }

  protected function getAllPermissions(array $permissions) {

    return Permission::whereIn('slug',$permissions)->get();
    
  }


}