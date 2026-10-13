<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\FieldStaff;
use App\Models\Inventory\GoodReturn;
use App\Models\Inventory\GoodReturnItem;
use App\Models\Inventory\Inventory;
use App\Models\Inventory\Location;
use App\Models\Logistic\Maintainance;
use App\Models\Logistic\Service;
use App\Models\Logistic\Truck;
use Illuminate\Http\Request;

class GoodReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $inventory= Inventory::all();
        $staff=FieldStaff::all();
        $location=Location::all();
        $truck=Truck::all();
        $return= GoodReturn::all();
       return view('inventory.good_return',compact('return','inventory','staff','location','truck'));
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
        $data['staff']=$request->staff;
        $data['location']=$request->location;
        $data['truck']=$request->location;
      
        $data['added_by']= auth()->user()->id;

        $return = GoodReturn::create($data);
        
       

        $nameArr =$request->item_id ;
        $qtyArr =$request->quantity ;

        if(!empty($nameArr)){
            for($i = 0; $i < count($nameArr); $i++){
                if(!empty($nameArr[$i])){


                    $items = array(
                        'item_id' => $nameArr[$i],
                        'quantity' =>    $qtyArr[$i],
                           'order_no' => $i,
                           'added_by' => auth()->user()->id,
                        'return_id' =>$return->id);

                        
                        $inv=Inventory::where('id',$nameArr[$i])->first();
                       

                      
                          GoodReturnItem::create($items);  
                          $q=$inv->quantity + $qtyArr[$i];
                        Inventory::where('id',$nameArr[$i])->update(['quantity' => $q]);
                        
                       
    
                }
            }
           
        }    

        return redirect(route('good_return.index'))->with(['success'=>'Good Return Created Successfully']);
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
        $data=GoodReturn::find($id);
        $inventory= Inventory::all();
        $items=GoodReturnItem::where('return_id',$id)->get();
        $staff=FieldStaff::all();
        $location=Location::all();
        $truck=Truck::all();
       return view('inventory.good_return',compact('items','inventory','data','id','staff','location','truck'));
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

        $return=GoodReturn::find($id);

        $data['date']=$request->date;
        $data['staff']=$request->staff;
        $data['location']=$request->location;
        $data['truck']=$request->location;
      

        $data['added_by']= auth()->user()->id;

        $return->update($data);
        
       

        $nameArr =$request->item_id ;
        $qtyArr =$request->quantity ;


        $remArr = $request->removed_id ;
        $expArr = $request->saved_id ;

        if (!empty($remArr)) {
            for($i = 0; $i < count($remArr); $i++){
               if(!empty($remArr[$i])){        
               GoodReturnItem::where('id',$remArr[$i])->delete();   
                            
                   }
               }
           }


           if (!empty($remArr)) {
            for($i = 0; $i < count($remArr); $i++){
               if(!empty($remArr[$i])){        
                $items = array(
                    'item_id' => $nameArr[$i],
                    'quantity' => $qtyArr[$i],
                       'order_no' => $i,
                       'added_by' => auth()->user()->id,
                    'return_id' =>$id);
                 
                $invr1=Inventory::where('id',$nameArr[$i])->get();
                foreach($invr1 as $inv_r){
               $q=$inv_r->quantity - $qtyArr[$i];
               Inventory::where('id',$nameArr[$i])->update(['quantity' => $q]);
                }
                   }
               }
           }


           
        if(!empty($nameArr)){
            for($i = 0; $i < count($nameArr); $i++){
                if(!empty($nameArr[$i])){


                    $items = array(
                        'item_id' => $nameArr[$i],
                        'quantity' => $qtyArr[$i],
                           'order_no' => $i,
                           'added_by' => auth()->user()->id,
                        'return_id' =>$id);
                       
                        $inv1=Inventory::where('id',$nameArr[$i])->get();
                        foreach($inv1 as $inv){
                       
                           

                            if(!empty($expArr[$i])){
                                $old1=GoodReturnItem::where('id',$expArr[$i])->get();
                                foreach($old1 as $old){
                            
                                if($old->quantity <= $qtyArr[$i]){
                                $q=$inv->quantity + ($qtyArr[$i]-$old->quantity);
                                Inventory::where('id',$nameArr[$i])->update(['quantity' => $q]);
                                }
                
                                else if($old->quantity > $qtyArr[$i]){
                                    $q=$inv->quantity - ($old->quantity - $qtyArr[$i]);
                                    Inventory::where('id',$nameArr[$i])->update(['quantity' => $q]);
                                    }
                                }
          
                             }
                          else{
                         
                         $q=$inv->quantity + $qtyArr[$i];
                         Inventory::where('id',$nameArr[$i])->update(['quantity' => $q]);
                          } 

                         
                         
                       

                    }
                       
                   
    
    
                }
            }
           
        }    



        if(!empty($nameArr)){
            for($i = 0; $i < count($nameArr); $i++){
                if(!empty($nameArr[$i])){


                    $items = array(
                        'item_id' => $nameArr[$i],
                        'quantity' => $qtyArr[$i],
                           'order_no' => $i,
                           'added_by' => auth()->user()->id,
                        'return_id' =>$id);
                       
                        $inv=Inventory::where('id',$nameArr[$i])->first();
                       


                            

                            if(!empty($expArr[$i])){
                                GoodReturnItem::where('id',$expArr[$i])->update($items);                              
                             }
                          else{
                         GoodReturnItem::create($items);  
                       
                          }                         
                         
                      
    
                }
            }
           
        }    




        return redirect(route('good_return.index'))->with(['success'=>'Good Return Updated Successfully']);
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

        GoodReturnItem::where('return_id', $id)->delete();

        $return=  GoodReturn::find($id);
        $return->delete();

        return redirect(route('good_return.index'))->with(['success'=>'Good Return Deleted Successfully']);
    }

    public function findService(Request $request)
    {

 switch ($request->id) {
        case 'Service':
              $type_id= Service::where('status','=','0')->get();                                                                                    
               return response()->json($type_id);
	                  
            break;

       case 'Maintenance':
           $type_id= Maintainance::where('status','=','0')->get(); 
                return response()->json($type_id);
	                  
            break;

    

    }

}
    
}
