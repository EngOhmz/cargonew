<?php

namespace App\Http\Controllers\authorization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Models\SystemModule;

class RoleController extends Controller
{  

    public function index()
    {
        // Only show roles for the current company
        $roles = Role::where('company_id', auth()->user()->company_id)->get();
        $permissions = Permission::all();
        $modules = SystemModule::all();
        return view('authorization.role.index', compact('roles', 'permissions', 'modules'));
    }

    public function create(Request $request)
    {

        $role = Role::find($request->role_id);
        if($role->added_by == auth()->user()->id){
        if (isset($request->permissions)) {
            $role->refreshPermissions($request->permissions);
        } else {
            $role->permissions()->detach();
        }
        $message = "permission updated successfully";
        $type = "success";
       }else{
           $message = "You dont have permission to perform this action";
           $type = "error";
       }

        return redirect()->back()->with([$type=>$message]);
    }

    public function store(Request $request)
    {
        $role = Role::create([
            'slug' => str_replace(' ', '-', $request->slug),
            'added_by'=>auth()->user()->id,
            'company_id'=>auth()->user()->company_id, // Set company_id
        ]);
        return redirect(route('roles.index'))->with('success', 'Role created successfully');
    }

    public function show($id)
    {
        $role = Role::find($id);
        $permissions = Permission::all();
        $modules = SystemModule::all();
        return view('authorization.role.assign', compact('permissions', 'modules', 'role'));
    }


    public function edit(Request $request)
    {
        //
    }

    public function update(Request $request, $id)
    {
        // Only allow updating roles from the same company
        $role = Role::where('id', $request->id)
            ->where('company_id', auth()->user()->company_id)
            ->firstOrFail();
        $role->slug = str_replace(' ', '-', $request->slug);
        $role->save();
        return redirect(route('roles.index'))->with('success', 'Role updated successfully');
    }

    public function destroy($id)
    {
        // Only allow deleting roles from the same company
        $role = Role::where('id', $id)
            ->where('company_id', auth()->user()->company_id)
            ->firstOrFail();
        $role->delete();
        return redirect(route('roles.index'))->with('success', 'Role deleted successfully');
    }
}
