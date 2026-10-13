<?php

namespace App\Http\Controllers\Tyre;

use App\Http\Controllers\Controller;
use App\Models\FieldStaff;
use App\Models\Location;
use App\Models\Truck;
use App\Models\Tyre\Tyre;
use App\Models\Tyre\TyreActivity;
use App\Models\Tyre\TyreBrand;
use App\Models\Tyre\TyreReturn;
use Illuminate\Http\Request;

class TyreReturnController extends Controller
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
        $location=Location::all();
        $truck=Truck::whereNotNull('tyre')->get();
        $return= TyreReturn::all();
       return view('tyre.good_return',compact('return','staff','location','truck'));
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

        $data=$request->post();
        $data['added_by']=auth()->user()->id;
        $data['status']='0';
        $tyre = TyreReturn::create($data);

        $name=Tyre::find($request->tyre_id);

        if(!empty($tyre)){
            $activity = TyreActivity::create(
                [ 
                    'added_by'=>auth()->user()->id,
                    'module_id'=>$tyre->id,
                    'module'=>'Tyre Return',
                    'activity'=>"Return of Tyre " .$name->serial_no. " is Created",
                    'date'=>$request->date,
                ]
                );                      
    }
     
            return redirect(route('tyre_return.index'))->with(['success'=>'Tyre Return Created Successfully']);
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
        $location=Location::all();
        $truck=Truck::whereNotNull('tyre')->get();
        $data= TyreReturn::find($id);
       return view('tyre.good_return',compact('data','staff','location','truck','id'));
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
        $tyre= TyreReturn::find($id);

        $data=$request->post();
        $data['added_by']=auth()->user()->id;
        $tyre->update($data);

        $name=Tyre::find($request->tyre_id);

        if(!empty($tyre)){
            $activity = TyreActivity::create(
                [ 
                    'added_by'=>auth()->user()->id,
                    'module_id'=>$tyre->id,
                    'module'=>'Tyre Return',
                    'activity'=>"Return of Tyre " .$name->serial_no. " is Updated",
                    'date'=>$request->date,
                ]
                );                      
    }
     
            return redirect(route('tyre_return.index'))->with(['success'=>'Tyre Return Updated Successfully']);
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

        $tyre = TyreReturn::find($id);

        if(!empty($tyre)){
            $activity = TyreActivity::create(
                [ 
                    'added_by'=>auth()->user()->id,
                    'module_id'=>$id,
                    'module'=>'Tyre Return',
                    'activity'=>"Tyre Deleted",
                   'date'=>date('Y-m-d'),
                ]
                );                      
}

        $tyre->delete();
        return redirect(route('tyre_return.index'))->with(['success'=>'Deleted Successfully']);
    }

    public function findPrice(Request $request)
    {
               $price= Tyre::where('truck_id',$request->id)->get();
                return response()->json($price);	                  

    }

    public function approve($id){
        //
        $tyre = TyreReturn::find($id);
        $data['status'] = 1;
        $tyre->update($data);

        $name=Tyre::where('id',$tyre->tyre_id)->first();

        $list['truck_id']=NULL;
        $list['status']='2';
        Tyre::where('id',$tyre->tyre_id)->update($list);
        
        
        $inv=TyreBrand::where('id',$name->brand_id)->first();
        $q=$inv->quantity + 1;
        TyreBrand::where('id',$name->brand_id)->update(['quantity' => $q]);

        Truck::where('id',$tyre->truck_id)->update(['tyre' => NULL,'staff'=> NULL]);

        if(!empty($tyre)){
           $activity = TyreActivity::create(
               [ 
                   'added_by'=>auth()->user()->id,
                   'module_id'=>$tyre->id,
                   'module'=>'Tyre Brand',
                   'activity'=>"Return of Tyre " .$name->serial_no. " is Approved",
                   'date'=>date('Y-m-d'),

               ]
               );                      
}
 
        return redirect(route('tyre_return.index'))->with(['success'=>'Return of Tyre Approved Successfully']);
    }

}
