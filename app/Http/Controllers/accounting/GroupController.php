<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Farmer;
use App\Models\User;
use App\Models\Group;
use App\Models\gmember;
use DB;
class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = DB::table('groups')->select('id','name', DB::raw('count(*) as total'))->groupBy('name')->get();
        return view('agrihub.manage-group')->with('group',$data);
        //print_r($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
                
        $this->validate($request,[
            'name'=>'required:unique'  
        ]); 
        
        //$data=$this->request();
        //$data['user_id'] =auth()->user()->id;
        //$farmer= Farmer::create($data);
      
        $group= new Group();

        $group->name=$request->input('name');
        $group->user_id=$user_id=auth()->user()->id;
        $group->save();
        if($group)
        {
             $messagev="New group of farmer registered successful";
            return redirect('manage-group')->with('messagev',$messagev);
       
        }
         else
     {
            $messager="Failed to register new Group";
            return redirect('manage-group')->with('messager',$messager);
        }
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
        $data=Group::find($id);
         $this->validate($request,[
            'name'=>'required',
            
        ]); 
       
        $result=$request->all();
        //print_r($result);
        $result['user_id']=auth()->user()->id;
        
        $data->update($result);
         //retrieve data for manage user page
        $user_id=auth()->user()->id;
        $user=User::find($user_id);
        //Validate update of data 
        if($data)
        {
            $messagev="Success Updated'";
            return redirect('manage/group')->with('messagev',$messagev);
        }
        else
        {
            return view('manage/group')->with('farmer',$user->farmer);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data=Group::find($id);
        $data->delete();
        if($data)
        {
            $messagev="Group deleted";
            return redirect('manage-group')->with('messagev',$messagev);
        }
    }
}
