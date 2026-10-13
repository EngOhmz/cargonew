<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    protected $fillable = [
         'slug','added_by','company_id'
    ];
    protected $touches = ['permissions'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_roles');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'roles_permissions');
    }

    public function givePermissionTo(array $permissions)
    {
        $permissions = $this->getAllPermissions($permissions);
        if ($permissions === null) {
            return $this;
        }
        $this->permissions()->saveMany($permissions);
        return $this;
    }

    protected function getAllPermissions(array $permissions)
    {
        return Permission::whereIn('id', $permissions)->get();
    }

    // Getting all permission

    public function getPermissions(array $permissions)
    {
        return Permission::whereIn('slug', $permissions)->get();
    }

    public function hasAccess($permission)
    {
        if (in_array($this->slug, ['superadmin', 'platform_admin'], true)) {
            return true;
        }
        return (bool)$this->permissions()->where('slug', $permission)->count();
    }

    public function hasPermissionId($id)
    {
        if (in_array($this->slug, ['superadmin', 'platform_admin'], true)) {
            return true;
        }
        return (bool)$this->permissions()->where('permissions.id', $id)->count();
    }

    public function hasModuleAccess($moduleSlug, $permissionSlug = 'read')
    {
        if (in_array($this->slug, ['superadmin', 'platform_admin'], true)) {
            return true;
        }
        return (bool)$this->permissions()
            ->whereHas('modules', function($q) use ($moduleSlug) {
                $q->where('slug', $moduleSlug);
            })
            ->where('slug', $permissionSlug)
            ->count();
    }

    public function refreshPermissions(array $permissions)
    {
        $this->permissions()->detach();
        return $this->givePermissionTo($permissions);
    }
}
