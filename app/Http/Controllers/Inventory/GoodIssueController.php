<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\GoodIssue;
use App\Models\Inventory\GoodIssueItem;
use App\Models\Inventory\Inventory;
use App\Models\Inventory\Location;
use App\Models\Logistic\Maintainance;
use App\Models\Inventory\Service;
use Illuminate\Http\Request;

class GoodIssueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $location=Location::all();
        $inventory= Inventory::all();
        $issue= GoodIssue::all();
       return view('inventory.good_issue',compact('issue','inventory','location'));
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
        $data['location']=$request->location;    
        $data['type']=$request->type;
        $data['type_id']=$request->type_id;

        if($request->type == 'Service'){
         $service= Service::where('id',$request->type_id)->first();
          $data['staff']=$service->mechanical;
        }
        else if($request->type == 'Maintenance'){
            $maintain=Maintainance::where('id',$request->type_id)->first();
             $data['staff']= $maintain->mechanical;
           }

        $data['added_by']= auth()->user()->id;

        $issue = GoodIssue::create($data);
        
       

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
                        'issue_id' =>$issue->id);

                        
                        $inv=Inventory::where('id',$nameArr[$i])->first();
                       

                        if(($qtyArr[$i] <= $inv->quantity)){                       
                          GoodIssueItem::create($items);  
                          $q=$inv->quantity - $qtyArr[$i];
                        Inventory::where('id',$nameArr[$i])->update(['quantity' => $q]);
                        }
                        else{
                            return redirect(route('good_issue.index'))->with(['error'=>'You have exceeded the Quantity']);
                        }
    
                }
            }
           
        }    

        return redirect(route('good_issue.index'))->with(['success'=>'Good Issue Created Successfully']);
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
        $data=GoodIssue::find($id);
        $location=Location::all();
        $inventory= Inventory::all();
        $items=GoodIssueItem::where('issue_id',$id)->get();
       return view('inventory.good_issue',compact('items','inventory','location','data','id'));
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

        $issue=GoodIssue::find($id);

        $data['date']=$request->date;
        $data['location']=$request->location;    
        $data['type']=$request->type;
        $data['type_id']=$request->type_id;

        if($request->type == 'Service'){
         $service= Service::where('id',$request->type_id)->first();
          $data['staff']=$service->mechanical;
        }
        else if($request->type == 'Maintenance'){
            $maintain=Maintainance::where('id',$request->type_id)->first();
             $data['staff']= $maintain->mechanical;
           }

        $data['added_by']= auth()->user()->id;

        $issue->update($data);
        
       

        $nameArr =$request->item_id ;
        $qtyArr =$request->quantity ;


        $remArr = $request->removed_id ;
        $expArr = $request->saved_id ;



           if (!empty($remArr)) {
            for($i = 0; $i < count($remArr); $i++){
               if(!empty($remArr[$i])){        
                $items = array(
                    'item_id' => $nameArr[$i],
                    'quantity' => $qtyArr[$i],
                       'order_no' => $i,
                       'added_by' => auth()->user()->id,
                    'issue_id' =>$id);
                 


                $invr1=Inventory::where('id',$remArr[$i])->get();
                foreach($invr1 as $inv_r){
               $q=$inv_r->quantity + $qtyArr[$i];
               Inventory::where('id',$nameArr[$i])->update(['quantity' => $q]);
                }
                   }
               }
           }

           
        if (!empty($remArr)) {
            for($i = 0; $i < count($remArr); $i++){
               if(!empty($remArr[$i])){        
               GoodIssueItem::where('id',$remArr[$i])->delete();   
                            
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
                        'issue_id' =>$id);
                       
                        $inv1=Inventory::where('id',$nameArr[$i])->get();
                        foreach($inv1 as $inv){
                       

                        if(($qtyArr[$i] <= $inv->quantity)){   
                            

                            if(!empty($expArr[$i])){
                                $old1=GoodIssueItem::where('id',$expArr[$i])->get();
                                foreach($old1 as $old){
                            
                                if($old->quantity <= $qtyArr[$i]){
                                $q=$inv->quantity - ($qtyArr[$i]-$old->quantity);
                                Inventory::where('id',$nameArr[$i])->update(['quantity' => $q]);
                                }
                
                                if($old->quantity > $qtyArr[$i]){
                                    $q=$inv->quantity + ($old->quantity - $qtyArr[$i]);
                                    Inventory::where('id',$nameArr[$i])->update(['quantity' => $q]);
                                    }
                                }
          
                             }
                          else{
                         
                         $q=$inv->quantity - $qtyArr[$i];
                         Inventory::where('id',$nameArr[$i])->update(['quantity' => $q]);
                          } 

                         
                         
                        }
                        else{
                            return redirect(route('good_issue.index'))->with(['error'=>'You have exceeded the Quantity']);
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
                        'issue_id' =>$id);
                       
                        $inv=Inventory::where('id',$nameArr[$i])->first();
                       

                        if(($qtyArr[$i] <= $inv->quantity)){   
                            

                            if(!empty($expArr[$i])){
                                GoodIssueItem::where('id',$expArr[$i])->update($items);                              
                             }
                          else{
                         GoodIssueItem::create($items);  
                       
                          }                         
                         
                        }
                        else{
                            return redirect(route('good_issue.index'))->with(['error'=>'You have exceeded the Quantity']);
                        }
    
                }
            }
           
        }    




        return redirect(route('good_issue.index'))->with(['success'=>'Good Issue Updated Successfully']);
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
        GoodIssueItem::where('issue_id', $id)->delete();

        $issue =  GoodIssue::find($id);
        $issue->delete();

        return redirect(route('good_issue.index'))->with(['success'=>'Good Issue Deleted Successfully']);
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
