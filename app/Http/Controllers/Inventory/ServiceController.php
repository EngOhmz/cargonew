<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\FieldStaff;
use App\Models\Logistic\Service;
use App\Models\Logistic\ServiceItem;
use App\Models\Facility\Facility;
use App\Models\Inventory\Inventory;
use App\Models\Logistic\MechanicalReport;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class ServiceController extends Controller
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
        $data['date']=$request->date;
        $data['facility']=$request->facility;    
       
        $data['mechanical']=$request->mechanical;
        $data['history']=$request->history;
        $data['major']=$request->major;
        $data['status']='0';
        $data['type']="Service";
        $personel=Facility::where('id',$request->facility)->first();
        $data['personel']=$personel->personel;
        $data['added_by']= auth()->user()->id;
        $service = Service::create($data);
        
       

        $nameArr =$request->minor ;

        if(!empty($nameArr)){
            for($i = 0; $i < count($nameArr); $i++){
                if(!empty($nameArr[$i])){


                    $items = array(
                        'minor' => $nameArr[$i],
                           'order_no' => $i,
                           'added_by' => auth()->user()->id,
                        'service_id' =>$service->id);
                       
                    ServiceItem::create($items);  ;
    
    
                }
            }
           
        }    


        $itemArr =$request->item_name ;
     
        if(!empty($itemArr)){
            for($i = 0; $i < count($itemArr); $i++){
                if(!empty($itemArr[$i])){

                    $report = array(
                        'item_name' => $itemArr[$i],
                           'order_no' => $i,
                           'module' => 'Service',
                           'date' => $request->date,
                           'added_by' => auth()->user()->id,
                        'module_id' =>$service->id);
                       
                        MechanicalReport::create($report);  ;

                       
    
    
                }
            }
           
        }     


        Toastr::success('Service Created Successfully','Success');
        return redirect(route('facility.show', $service->facility));
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
        $service =  Service::find($id);

        $data['date']=$request->date;
        $data['facility']=$request->facility;    
       
        $data['mechanical']=$request->mechanical;
        $data['history']=$request->history;
        $data['major']=$request->major;
        $data['status']='0';
        $data['type']="Service";
        $personel=Facility::where('id',$request->facility)->first();
        $data['personel']=$personel->personel;
        $data['added_by']= auth()->user()->id;
        $service->update($data);
             

        $nameArr =$request->minor ;
        $remArr = $request->removed_id ;
        $expArr = $request->saved_id ;

        if (!empty($remArr)) {
            for($i = 0; $i < count($remArr); $i++){
               if(!empty($remArr[$i])){        
                ServiceItem::where('id',$remArr[$i])->delete();        
                   }
               }
           }


        if(!empty($nameArr)){
            for($i = 0; $i < count($nameArr); $i++){
                if(!empty($nameArr[$i])){


                    $items = array(
                        'minor' => $nameArr[$i],
                           'order_no' => $i,
                           'added_by' => auth()->user()->id,
                        'service_id' =>$id);
                       
                        if(!empty($expArr[$i])){
                            ServiceItem::where('id',$expArr[$i])->update($items);  
      
      }
      else{
        ServiceItem::create($items);     
      }
                   
    
    
                }
            }
           
        }   
        
        
        $itemArr =$request->item_name ;
        $invremArr = $request->removed_inv_id ;
             $invexpArr = $request->saved_inv_id ;
     
             if (!empty($invremArr)) {
                 for($i = 0; $i < count($invremArr); $i++){
                    if(!empty($invremArr[$i])){        
                    MechanicalReport::where('id',$invremArr[$i])->delete();        
                        }
                    }
                }
     
             if(!empty($itemArr)){
                 for($i = 0; $i < count($itemArr); $i++){
                     if(!empty($itemArr[$i])){
     
                         $report = array(
                            'item_name' => $itemArr[$i],
                            'order_no' => $i,
                            'module' => 'Service',
                            'date' => $request->date,
                            'added_by' => auth()->user()->id,
                         'module_id' =>$service->id);
     
                         if(!empty($invexpArr[$i])){
                                  MechanicalReport::where('id',$invexpArr[$i])->update($report);  
           
           }
           else{
            MechanicalReport::create($report);  ; 
           }
                            
                       
         
         
                     }
                 }
                
             }     
     

             Toastr::success('Service Updated Successfully','Success');
             return redirect(route('facility.show', $service->facility));
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
        ServiceItem::where('service_id', $id)->delete();
       MechanicalReport::where('module_id', $id)->where('module','Service')->delete();
        $service =  Service::find($id);
        $facility=$service->facility;
        $service->delete();

        Toastr::success('Service Deleted Successfully','Success');
        return redirect(route('facility.show', $facility));
    }

    public function approve($id)
    {
        //
        $service =  Service::find($id);
        $data['status'] = 1;
        $service->update($data);

        $report=MechanicalReport::where('module_id', $id)->where('module','Service')->get();
         foreach($report as $r){
            $inv=Inventory::where('id',$r->item_name)->first();
            $q=$inv->quantity - 1;
            Inventory::where('id',$r->item_name)->update(['quantity' => $q]);

         }

        Toastr::success('Service Completed Successfully','Success');
        return redirect(route('facility.show', $service->facility));
    }

}
