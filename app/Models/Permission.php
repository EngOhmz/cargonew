<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Permission extends Model
{
   //
   //use SoftDeletes;

    protected $fillable = [
        'slug', 'sys_module_id'
    ];

    protected $with = ['modules'];

    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'roles_permissions');
    }

    public function modules()
    {
        return $this->belongsTo('App\Models\SystemModule', 'sys_module_id');
    }
}
