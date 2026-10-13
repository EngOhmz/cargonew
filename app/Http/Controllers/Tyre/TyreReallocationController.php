<?php

namespace App\Http\Controllers\Tyre;

use App\Http\Controllers\Controller;
use App\Models\FieldStaff;
use App\Models\Truck;
use App\Models\Tyre\Tyre;
use App\Models\Tyre\TyreActivity;
use App\Models\Tyre\TyreBrand;
use App\Models\Tyre\TyreReallocation;
use Illuminate\Http\Request;

class TyreReallocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $staff=FieldStaff::all();
        $truck_s=Truck::whereNotNull('tyre')->get();
        $truck=Truck::all();
        $reallocation= TyreReallocation::all();
       return view('tyre.good_reallocation',compact('reallocation','staff','truck','truck_s'));
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
     if($request->source_truck != $request->destination_truck){
        $data=$request->post();
        $name=Tyre::where('truck_id',$request->source_truck)->first();

        $data['added_by']=auth()->user()->id;
        $data['status']='0';
        $data['tyre_id']=$name->id;
        $tyre = TyreReallocation::create($data);



        if(!empty($tyre)){
            $activity = TyreActivity::create(
                [ 
                    'added_by'=>auth()->user()->id,
                    'module_id'=>$tyre->id,
                    'module'=>'Tyre Reallocation',
                    'activity'=>"Reallocation of Tyre " .$name->serial_no. " is Created",
                    'date'=>$request->date,
                ]
                );                      
    }

            return redirect(route('tyre_reallocation.index'))->with(['success'=>'Tyre Reallocation Created Successfully']);
}

else{
    return redirect(route('tyre_reallocation.index'))->with(['error'=>'Source and Destination cannot be the same']);

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
        $staff=FieldStaff::all();
        $truck_s=Truck::whereNotNull('tyre')->get();
        $truck=Truck::all();
        $data= TyreReallocation::find($id);
       return view('tyre.good_reallocation',compact('data','staff','truck','truck_s','id'));
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

        if($request->source_truck != $request->destination_truck){
            $tyre= TyreReallocation::find($id);

            $data=$request->post();
            $name=Tyre::where('truck_id',$request->source_truck)->first();
    
            $data['added_by']=auth()->user()->id;
            $data['status']='0';
            $data['tyre_id']=$name->id;
            $tyre->update($data);
    
    
    
            if(!empty($tyre)){
                $activity = TyreActivity::create(
                    [ 
                        'added_by'=>auth()->user()->id,
                        'module_id'=>$tyre->id,
                        'module'=>'Tyre Reallocation',
                        'activity'=>"Reallocation of Tyre " .$name->serial_no. " is Updated",
                        'date'=>$request->date,
                    ]
                    );                      
        }
    
                return redirect(route('tyre_reallocation.index'))->with(['success'=>'Tyre Reallocation Updated Successfully']);
    }
    
    else{
        return redirect(route('tyre_reallocation.index'))->with(['error'=>'Source and Destination cannot be the same']);
    
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
        //
        $tyre = TyreReallocation::find($id);

        if(!empty($tyre)){
            $activity = TyreActivity::create(
                [ 
                    'added_by'=>auth()->user()->id,
                    'module_id'=>$id,
                    'module'=>'Tyre Reallocation',
                    'activity'=>"Tyre Deleted",
                   'date'=>date('Y-m-d'),
                ]
                );                      
}

        $tyre->delete();
        return redirect(route('tyre_reallocation.index'))->with(['success'=>'Deleted Successfully']);
    }

    public function approve($id){
        //
        $tyre = TyreReallocation::find($id);
        $data['status'] = 1;
        $tyre->update($data);
        
       Tyre::where('id',$tyre->tyre_id)->update(['truck_id' => $tyre->destination_truck]);
       Truck::where('id',$tyre->source_truck)->update(['tyre' => NULL,'staff'=> NULL]);

       $trk=Truck::where('id',$tyre->destination_truck)->first();
       if(!empty($trk->tyre)){
     
        
        $list['truck_id']=NULL;
        $list['status']='2';
        Tyre::where('id',$trk->tyre)->update($list);

        $name=Tyre::where('id',$trk->tyre)->first();

        $inv=TyreBrand::where('id',$name->brand_id)->first();
        $q=$inv->quantity + 1;
        TyreBrand::where('id',$name->brand_id)->update(['quantity' => $q]);

        Truck::where('id',$tyre->destination_truck)->update(['tyre' => $tyre->tyre_id,'staff'=> $tyre->staff]);

       }
       else if(empty($trk->tyre)){     

        Truck::where('id',$tyre->destination_truck)->update(['tyre' => $tyre->tyre_id,'staff'=> $tyre->staff]);

       }
        
        
       $a=Tyre::where('id',$tyre->tyre_id)->first();

        if(!empty($tyre)){
           $activity = TyreActivity::create(
               [ 
                   'added_by'=>auth()->user()->id,
                   'module_id'=>$tyre->id,
                   'module'=>'Tyre Reallocation',
                   'activity'=>"Reallocation of Tyre " .$a->serial_no. " is Approved",
                   'date'=>date('Y-m-d'),

               ]
               );                      
}
 
        return redirect(route('tyre_reallocation.index'))->with(['success'=>'Return of Tyre Approved Successfully']);
    }
}
