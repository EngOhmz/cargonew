<?php

namespace App\Http\Controllers\authorization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Application;
//use App\Region;



class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        
        $users = User::all();

        return view('authorization.users.index',Compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $roles = Role::all();
        $department = Department::all();
        //$region = Region::all();
        return view('authorization.users.add',Compact('roles','department'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {  
        $validatedData = $request->validate([
           
            'name' => 'required|max:255|min:3|string',
            //'role' => 'required|string',
           
            'email' => 'required|string|min:3|unique:users', 
           
           // 'password' => 'required|string|min:6|confirmed',
           
          
        ]);
        //
        $user = User::create([
            'name' => $request['name'],
           
            'email' => $request['email'],
            
            'password' => Hash::make($request['password']),
            
            'added_by' => auth()->user()->id,
            'status' => 1,
            'department_id' => $request['department_id'],
           
            'designation_id' => $request['designation_id'],
        ]);

        if (!$user) {
          //  return redirect(route('users.index'));
        }

        $user->roles()->attach($request['role']);
        return redirect(route('users.index'));
       
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
        $users = User::all();

        return view('authorization.users.index2',Compact('users'));
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
        $role = Role::all();
       
        //$user = User::with('Role')->where('id',$id)->get();
        $user = User::all()->where('id',$id);
        $user_id = User::find($id);
        $department = Department::all();
        $designation= Designation::where('department_id', $user_id->department_id)->get();
        return view('authorization.users.edit',compact('user','role','department','designation'));
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
        //
        $user = User::findOrFail($id);
        $user->name = $request['name'];
       
        $user->email = $request['email'];
        $user->department_id = $request['department_id'];
           
        $user->designation_id = $request['designation_id'];
        
        $user->save();

        if (!$user) {
           
        }
        $user->roles()->detach();
        $user->roles()->attach($request['role']);
        return redirect(route('users.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $user = User::find($id);
        $user->delete();
        return redirect(route('users.index'));
    }

    public function findDepartment(Request $request)
    {

        $district= Designation::where('department_id',$request->id)->get();                                                                                    
               return response()->json($district);

}

}
