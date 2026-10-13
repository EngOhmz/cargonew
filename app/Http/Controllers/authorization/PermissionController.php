<?php

namespace App\Http\Controllers\authorization;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\SystemModule;

class PermissionController extends Controller
{  
    public function __construct()
    {
       
        
    }
    public function index()
    {  // , compact('permissions', 'modules')
        $permissions = Permission::all();
        $modules = SystemModule::all();
        return view('authorization.permission.index', compact('permissions', 'modules'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $role = Permission::create([
            'slug' => str_replace(' ', '-', $request->slug),
            'sys_module_id' => $request->module_id,
        ]);
        return redirect(route('permissions.index'));
    }

    public function show(Permission $permission)
    {
        //
    }

    public function edit(Request $request)
    {
        //
    }


    public function update(Request $request, $id)
    {
        $role = Permission::find($request->id);
        $role->slug = str_replace(' ', '-', $request->slug);
        $role->sys_module_id = $request->module_id;
        $role->save();
        return redirect(route('permissions.index'));
    }

    public function destroy($id)
    {
        $role = Permission::find($id);
        $role->delete();
        return redirect(route('permissions.index'));
    }
}
