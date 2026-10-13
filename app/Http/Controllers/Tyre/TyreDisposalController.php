<?php

namespace App\Http\Controllers\Tyre;

use App\Http\Controllers\Controller;
use App\Models\FieldStaff;
use App\Models\Tyre\Tyre;
use App\Models\Tyre\TyreActivity;
use App\Models\Tyre\TyreBrand;
use App\Models\Tyre\TyreDisposal;
use Illuminate\Http\Request;

class TyreDisposalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $inventory= Tyre::where('status','0')->orwhere('status','2')->get();
        $staff=FieldStaff::all();
        $disposal= TyreDisposal::all();
       return view('tyre.good_disposal',compact('disposal','inventory','staff'));
        
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
        $data['status'] = 0;
        $inv=Tyre::where('id',$request->tyre_id)->first();
        $data['location']= $inv->location;
        $data['added_by']=auth()->user()->id;

 
        $disposal= TyreDisposal::create($data);
  
       
        if(!empty($disposal)){
            $activity = TyreActivity::create(
                [ 
                    'added_by'=>auth()->user()->id,
                    'module_id'=>$disposal->id,
                    'module'=>'Good Disposal',
                    'activity'=>"Disposal of Tyre " .$inv->serial_no. " is Created",
                    'date'=>$request->date,
                ]
                );                      
}

        return redirect(route('tyre_disposal.index'))->with(['success'=>'Good Disposal Created Successfully']);
      

        
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

        $inventory= Tyre::where('status','0')->orwhere('status','2')->get();
        $staff=FieldStaff::all();
        $data= TyreDisposal::find($id);
       return view('tyre.good_disposal',compact('data','inventory','staff','id'));
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
        $disposal= TyreDisposal::find($id);

        $data = $request->all();
        $inv=Tyre::where('id',$request->tyre_id)->first();
        $data['location']= $inv->location;
        $data['added_by']=auth()->user()->id;

       
 
        $disposal->update($data);
  
       
        if(!empty($disposal)){
            $activity = TyreActivity::create(
                [ 
                    'added_by'=>auth()->user()->id,
                    'module_id'=>$disposal->id,
                    'module'=>'Good Disposal',
                    'activity'=>"Disposal of Tyre " .$inv->serial_no. " is Updated",
                    'date'=>$request->date,
                ]
                );                      
}

        return redirect(route('tyre_disposal.index'))->with(['success'=>'Good Disposal Updated Successfully']);


       
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

        $disposal= TyreDisposal::find($id);
        $inv=Tyre::where('id', $disposal->tyre_id)->first();
        if(!empty($disposal)){
            $activity = TyreActivity::create(
                [ 
                    'added_by'=>auth()->user()->id,
                    'module_id'=>$disposal->id,
                    'module'=>'Good Disposal',
                    'activity'=>"Delete of Tyre",
                   'date'=>date('Y-m-d'),
                ]
                );                      
}

        $disposal->delete();

        return redirect(route('tyre_disposal.index'))->with(['success'=>'Good Disposal Deleted Successfully']);
    }

    public function approve($id)
    {
        //

        $disposal = TyreDisposal::find($id);
        $data['status'] = 1;
        $disposal->update($data);


        $name=Tyre::where('id', $disposal->tyre_id)->first();
        $inv=TyreBrand::where('id',$name->brand_id)->first();
        $q=$inv->quantity - 1;
        TyreBrand::where('id',$name->brand_id)->update(['quantity' => $q]);

        Tyre::where('id', $disposal->tyre_id)->update(['status' => '3']);

        if(!empty($disposal)){
            $activity = TyreActivity::create(
                [ 
                    'added_by'=>auth()->user()->id,
                    'module_id'=>$disposal->id,
                    'module'=>'Good Disposal',
                    'activity'=>"Disposal of Tyre " .$name->serial_no." is Approved",
                   'date'=>date('Y-m-d'),
                ]
                );                      
}


       
        return redirect(route('tyre_disposal.index'))->with(['success'=>'Approved Successfully']);
    }

}
