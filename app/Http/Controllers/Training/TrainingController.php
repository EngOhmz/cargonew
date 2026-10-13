<?php

namespace App\Http\Controllers\Training;

use App\Http\Controllers\Controller;
use App\Models\Accounting\AccountCodes;
use App\Models\Training\Training;
use App\Models\Leave\LeaveCategory;
use App\Models\Fuel\Refill;
use App\Models\JournalEntry;
use App\Models\Route;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Expenses;
use Brian2694\Toastr\Facades\Toastr;

class TrainingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $staff=User::where('id','!=',1)->get();    
        $training = Training::all();    
        return view('training.training',compact('staff','training'));
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
        $data = $request->all();
        $data['added_by']=auth()->user()->id;
        $data['status']=0;

        if ($request->hasFile('attachment')) {
                    $file=$request->file('attachment');
                    $fileType=$file->getClientOriginalExtension();
                    $fileName=rand(1,1000).date('dmyhis').".".$fileType;
                    $name=$fileName;
                    $path = public_path(). "/assets/files/training";
                    $file->move($path, $fileName );
                    
                    $data['attachment'] = $name;
                }else{
                        $data['attachment'] = null;
                }


        $training= Training::create($data);


      
 
        return redirect(route('training.index'))->with(['success'=>'Training Created Successfully']);
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
        $data = Training::find($id);
        $staff=User::where('id','!=',1)->get();    
        
        return view('training.training',compact('staff','data','id'));
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
        $training= Training::find($id);

   $data = $request->all();
      $data['added_by']=auth()->user()->id;

        if ($request->hasFile('attachment')) {
                    $file=$request->file('attachment');
                    $fileType=$file->getClientOriginalExtension();
                    $fileName=rand(1,1000).date('dmyhis').".".$fileType;
                    $name=$fileName;
                    $path = public_path(). "/assets/files/training";
                    $file->move($path, $fileName );
                    
                    $data['attachment'] = $name;
                }else{
                        $data['attachment'] = null;
                }

                


        $training->update($data);
        Toastr::success('Training Updated Successfully','Success');
        return redirect(route('training.index'));

       
        
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
        $training=  Training::find($id);
        $training->delete();
    }

   public function start($id)
    {
        //
        $training = Training::find($id);
        $data['status'] = 1;
        $training->update($data);
        return redirect(route('training.index'))->with(['success'=>'Training Started Successfully']);
    }

    public function approve($id)
    {
        //
        $training = Training::find($id);
        $data['status'] = 2;
        $training->update($data);
        return redirect(route('training.index'))->with(['success'=>'Completed Successfully']);
    }

     public function reject($id)
    {
        //
         $training = Training::find($id);
        $data['application_status'] = 3;
        $training->update($data);
        return redirect(route('training.index'))->with(['success'=>'Terminated Successfully']);
    }

}
