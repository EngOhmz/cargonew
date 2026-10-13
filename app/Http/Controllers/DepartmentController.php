<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\SystemModule;
use Brian2694\Toastr\Facades\Toastr;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
        $permissions = Department::all();
        return view('authorization.department.index', compact('permissions'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $role = Department::create([
            'name' => $request->name,
        ]);
        Toastr::success('Department Created Successfully','Success');
        return redirect(route('departments.index'));
    }

   

    public function edit(Request $request)
    {
        //
    }


    public function update(Request $request, $id)
    {
        $role = Department::find($request->id);
        $role->name = $request->name;
        $role->update();
        Toastr::success('Department Updated Successfully','Success');
        return redirect(route('departments.index'));
    }

    public function destroy($id)
    {
        $role = Department::find($id);
        $role->delete();
        Toastr::success('Department Deleted Successfully','Success');
        return redirect(route('departments.index'));
    }
}
