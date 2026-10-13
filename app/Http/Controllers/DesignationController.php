<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Designation;
use App\Models\SystemModule;
use Brian2694\Toastr\Facades\Toastr;

class DesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $permissions = Designation::all();
        $department = Department::all();
       return view('authorization.designation.index', compact('permissions','department'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $role = Designation::create([
            'name' => $request->name,
           'department_id' => $request->department_id,
        ]);
        Toastr::success('Designation Created Successfully','Success');
        return redirect(route('designations.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   
    public function update(Request $request, $id)
    {
        $role = Designation::find($request->id);
        $role->name = $request->name;
        $role->department_id = $request->department_id;
        $role->update();
        Toastr::success('Designation Updated Successfully','Success');
        return redirect(route('designations.index'));
    }

    public function destroy($id)
    {
        $role = Designation::find($id);
        $role->delete();
        Toastr::success('Designation Deleted Successfully','Success');
        return redirect(route('designations.index'));
    }
}
