<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\FieldStaff;
use App\Models\Logistic\Maintainance;
use App\Models\Facility\Facility;
use App\Models\Inventory\Inventory;
use App\Models\Logistic\MechanicalReport;
use App\Models\Logistic\Service;
use App\Models\Logistic\ServiceItem;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class MaintainanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
  
       
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
        $personel=Facility::where('id',$request->facility)->first();
        $data['personel']=$personel->personel;
        $data['status']='0';
        $data['added_by']=auth()->user()->id;
        $data['type']="Maintainance";
        $maintain= Maintainance::create($data);
        Toastr::success('Maintainance Created Successfully','Success');
        return redirect(route('facility.show', $maintain->facility));
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
        //
        $maintain =  Maintainance::find($id);

        $data = $request->all();
        $personel=Facility::where('id',$request->facility)->first();
        $data['personel']=$personel->personel;
        $data['status']='0';
        $data['added_by']=auth()->user()->id;
        $data['type']="Maintainance";
        $maintain->update($data);
        Toastr::success('Maintainance Updated Successfully','Success');
        return redirect(route('facility.show', $maintain->facility));
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
        MechanicalReport::where('module_id', $id)->where('module','Maintainance')->delete();
        $maintain =  Maintainance::find($id);
        $facility=$maintain->facility;
        $maintain->delete();
        Toastr::success('Maintainance Deleted Successfully','Success');
        return redirect(route('facility.show', $facility));
    }

    public function discountModal(Request $request)
    {
                 $id=$request->id;
                 $type = $request->type;
                
                if($type == 'maintainance'){                 
                $staff = FieldStaff::all();
               $name = Maintainance::find($id);
     
                    return view('facility.editmaintainance',compact('id','name','staff'));
      }
            elseif($type == 'service'){
                $name=Service::find($id);
                $items=ServiceItem::where('service_id',$id)->get();
               $inv=MechanicalReport::where('module_id',$id)->where('module','Service')->get();
               $staff = FieldStaff::all();
               $inv_name = Inventory::all();
                    return view('facility.editservice',compact('id','name','staff','items','inv','inv_name'));
      }
                 elseif($type == 'report'){
                    $name = Inventory::all();
                 return view('facility.report',compact('id','name'));
}
                 }

    public function approve($id)
    {
        //
        $maintain = Maintainance::find($id);
        $data['status'] = 1;
        $maintain->update($data);
        Toastr::success('Maintainance Completed Successfully','Success');
        return redirect(route('facility.show', $maintain->facility));
    }

    public function save_report(Request $request)
    {
        //


        $nameArr =$request->item_name ;
     
        if(!empty($nameArr)){
            for($i = 0; $i < count($nameArr); $i++){
                if(!empty($nameArr[$i])){

                    $items = array(
                        'item_name' => $nameArr[$i],
                           'order_no' => $i,
                           'added_by' => auth()->user()->id,
                           'module' => 'Maintainance',
                           'date' => $request->date,
                        'module_id' =>$request->maintainance_id);
                       
                    MechanicalReport::create($items);  ;
    
                    $inv=Inventory::where('id',$nameArr[$i])->first();
                    $q=$inv->quantity - 1;
                    Inventory::where('id',$nameArr[$i])->update(['quantity' => $q]);
                }
            }
           
        }    

        
        $maintain = Maintainance::find($request->maintainance_id);
        $data['report'] = 1;
        $maintain->update($data);

        Toastr::success('Mechanical Report Created Successfully','Success');
        return redirect(route('facility.show', $maintain->facility));
        
    }

}
