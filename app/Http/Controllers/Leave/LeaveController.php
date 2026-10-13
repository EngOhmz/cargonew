<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Models\Accounting\AccountCodes;
use App\Models\Leave\Leave;
use App\Models\Leave\LeaveCategory;
use App\Models\Fuel\Refill;
use App\Models\JournalEntry;
use App\Models\Route;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Expenses;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $category = LeaveCategory::all(); 
        $staff=User::where('id','!=',1)->get();    
        $leave = Leave::all();    
        return view('leave.leave',compact('category','staff','leave'));
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
        $data = $request->all();
        $data['added_by']=auth()->user()->id;
        $data['application_status']='1';



        if ($request->hasFile('attachment')) {
                    $file=$request->file('attachment');
                    $fileType=$file->getClientOriginalExtension();
                    $fileName=rand(1,1000).date('dmyhis').".".$fileType;
                    $name=$fileName;
                    $path = public_path(). "/assets/files/leave";
                    $file->move($path, $fileName );
                    
                    $data['attachment'] = $name;
                }else{
                        $data['attachment'] = null;
                }


        $leave= Leave::create($data);


      
 
        return redirect(route('leave.index'))->with(['success'=>'Leave Created Successfully']);
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

    public function discountModal(Request $request)
    {
               
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
        $data =  Leave::find($id);
          $category = LeaveCategory::all(); 
        $staff=User::where('id','!=',1)->get();    
        
        return view('leave.leave',compact('category','staff','data','id'));
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
        $leave=  Leave::find($id);

   $data = $request->all();
      $data['added_by']=auth()->user()->id;

        if ($request->hasFile('attachment')) {
                    $file=$request->file('attachment');
                    $fileType=$file->getClientOriginalExtension();
                    $fileName=rand(1,1000).date('dmyhis').".".$fileType;
                    $name=$fileName;
                    $path = public_path(). "/assets/files/leave";
                    $file->move($path, $fileName );
                    
                    $data['attachment'] = $name;
                }else{
                        $data['attachment'] = null;
                }

                


        $leave->update($data);
        return redirect(route('leave.index'))->with(['success'=>'Leave Updated Successfully']);

       
        
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
        $leave=  Leave::find($id);
        $leave->delete();
    }

    public function category(Request $request)
    {
        //
        $data = $request->all();
        $data['added_by']=auth()->user()->id;
        $category =LeaveCategory::create($data);
       
       if ($request->ajax()) {

            return response()->json($category);
       }
    }

    public function approve($id)
    {
        //
        $leave = Leave::find($id);
        $data['application_status'] = 2;
    $data['approve_by'] = auth()->user()->id;;
        $leave->update($data);
        return redirect(route('leave.index'))->with(['success'=>'Approved Successfully']);
    }

     public function reject($id)
    {
        //
        $leave = Leave::find($id);
        $data['application_status'] = 3;
        $leave->update($data);
        return redirect(route('leave.index'))->with(['success'=>'Rejected Successfully']);
    }

}
