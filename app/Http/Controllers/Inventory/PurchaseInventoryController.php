<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Accounting\AccountCodes;
use App\Models\Payments\Currency;
use App\Models\Inventory\Inventory;
use App\Models\Inventory\InventoryHistory;
use App\Models\Inventory\InventoryPayment;
use App\Models\Accounting\JournalEntry;
use App\Models\Inventory\FieldStaff;
use App\Models\Inventory\Location;
use App\Models\Payments\Payment_methodes;
use App\Models\Purchase\Purchase_items;
use App\Models\Purchase\PurchaseInventory;
use App\Models\Purchase\PurchaseItemInventory;
use App\Models\Purchase\Supplier;
use App\Models\Inventory\InventoryList;
use App\Models\Inventory\Truck;
use PDF;

use Illuminate\Http\Request;

class PurchaseInventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $currency= Currency::all();
        $purchases=PurchaseInventory::all();
        $supplier=Supplier::all();
        $name = Inventory::all();
        $location = Location::all();
        $type="";
       return view('inventory.manage_purchase_inv',compact('name','supplier','currency','purchases','location','type'));
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

        $data['reference_no']='1';
        $data['supplier_id']=$request->supplier_id;
        $data['purchase_date']=$request->purchase_date;
        $data['due_date']=$request->due_date;
        $data['location']=$request->location;
        $data['exchange_code']=$request->exchange_code;
        $data['exchange_rate']=$request->exchange_rate;
        $data['purchase_type']=$request->purchase_type;
        $data['purchase_amount']='1';
        $data['due_amount']='1';
        $data['purchase_tax']='1';
        $data['status']='0';
        $data['good_receive']='0';
        $data['added_by']= auth()->user()->id;

        $purchase = PurchaseInventory::create($data);
        
        $amountArr = str_replace(",","",$request->amount);
        $totalArr =  str_replace(",","",$request->tax);

        $nameArr =$request->item_name ;
        $qtyArr = $request->quantity  ;
        $priceArr = $request->price;
        $rateArr = $request->tax_rate ;
        $unitArr = $request->unit  ;
        $costArr = str_replace(",","",$request->total_cost)  ;
        $taxArr =  str_replace(",","",$request->total_tax );

        
        $savedArr =$request->item_name ;
        
        $cost['purchase_amount'] = 0;
        $cost['purchase_tax'] = 0;
        if(!empty($nameArr)){
            for($i = 0; $i < count($nameArr); $i++){
                if(!empty($nameArr[$i])){
                    $cost['purchase_amount'] +=$costArr[$i];
                    $cost['purchase_tax'] +=$taxArr[$i];

                    $items = array(
                        'item_name' => $nameArr[$i],
                        'quantity' =>   $qtyArr[$i],
                        'tax_rate' =>  $rateArr [$i],
                         'unit' => $unitArr[$i],
                           'price' =>  $priceArr[$i],
                        'total_cost' =>  $costArr[$i],
                        'total_tax' =>   $taxArr[$i],
                         'items_id' => $savedArr[$i],
                           'order_no' => $i,
                           'added_by' => auth()->user()->id,
                        'purchase_id' =>$purchase->id);
                       
                     PurchaseItemInventory::create($items);  ;
    
    
                }
            }
            $cost['reference_no']= "PUR_INV-".$purchase->id."-".$data['purchase_date'];
            $cost['due_amount'] =  $cost['purchase_amount'] + $cost['purchase_tax'];
            PurchaseInventory::where('id',$purchase->id)->update($cost);
        }    

        
        return redirect(route('purchase_inventory.show',$purchase->id));
        
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
        $purchases = PurchaseInventory::find($id);
        $purchase_items=PurchaseItemInventory::where('purchase_id',$id)->get();
        $payments=InventoryPayment::where('purchase_id',$id)->get();
        
        return view('inventory.purchase_inv_details',compact('purchases','purchase_items','payments'));
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
        $currency= Currency::all();
        $supplier=Supplier::all();
        $name = Inventory::all();
        $location = Location::all();
        $data=PurchaseInventory::find($id);
        $items=PurchaseItemInventory::where('purchase_id',$id)->get();
        $type="";
       return view('inventory.manage_purchase_inv',compact('name','supplier','currency','location','data','id','items','type'));
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

        if($request->type == 'receive'){
            $purchase = PurchaseInventory::find($id);
            $data['supplier_id']=$request->supplier_id;
            $data['purchase_date']=$request->purchase_date;
            $data['due_date']=$request->due_date;
            $data['location']=$request->location;
            $data['exchange_code']=$request->exchange_code;
            $data['exchange_rate']=$request->exchange_rate;
            $data['purchase_amount']='1';
            $data['due_amount']='1';
            $data['purchase_tax']='1';
            $data['good_receive']='1';
            $data['added_by']= auth()->user()->id;
    
            $purchase->update($data);
            
            $amountArr = str_replace(",","",$request->amount);
            $totalArr =  str_replace(",","",$request->tax);
    
            $nameArr =$request->item_name ;
            $qtyArr = $request->quantity  ;
            $priceArr = $request->price;
            $rateArr = $request->tax_rate ;
            $unitArr = $request->unit  ;
            $costArr = str_replace(",","",$request->total_cost)  ;
            $taxArr =  str_replace(",","",$request->total_tax );
            $remArr = $request->removed_id ;
            $expArr = $request->saved_items_id ;
            $savedArr =$request->item_name ;
            
            $cost['purchase_amount'] = 0;
            $cost['purchase_tax'] = 0;
    
            if (!empty($remArr)) {
                for($i = 0; $i < count($remArr); $i++){
                   if(!empty($remArr[$i])){        
                    PurchaseItemInventory::where('id',$remArr[$i])->delete();        
                       }
                   }
               }
    
            if(!empty($nameArr)){
                for($i = 0; $i < count($nameArr); $i++){
                    if(!empty($nameArr[$i])){
                        $cost['purchase_amount'] +=$costArr[$i];
                        $cost['purchase_tax'] +=$taxArr[$i];
    
                        $items = array(
                            'item_name' => $nameArr[$i],
                            'quantity' =>   $qtyArr[$i],
                            'tax_rate' =>  $rateArr [$i],
                             'unit' => $unitArr[$i],
                               'price' =>  $priceArr[$i],
                            'total_cost' =>  $costArr[$i],
                            'total_tax' =>   $taxArr[$i],
                             'items_id' => $savedArr[$i],
                               'order_no' => $i,
                               'added_by' => auth()->user()->id,
                            'purchase_id' =>$id);
                           
                            if(!empty($expArr[$i])){
                                PurchaseItemInventory::where('id',$expArr[$i])->update($items);  
          
          }
          else{
            PurchaseItemInventory::create($items);   
          }
                      
                  if(!empty($qtyArr[$i])){
            for($x = 1; $x <= $qtyArr[$i]; $x++){    
                $name=Inventory::where('id', $savedArr[$i])->first();
                $dt=date('Y',strtotime($data['purchase_date']));
                    $lists = array(
                        'serial_no' => $name->name."_" .$id."_".$x."_" .$dt,                      
                         'brand_id' => $savedArr[$i],
                           'added_by' => auth()->user()->id,
                           'purchase_id' =>   $id,
                         'purchase_date' =>  $data['purchase_date'],
                           'location' => $data['location'],
                           'status' => '0');
                       
     
                    InventoryList::create($lists);   
      
                }
            }
         
  
                    }
                }
                $cost['due_amount'] =  $cost['purchase_amount'] + $cost['purchase_tax'];
                PurchaseInventory::where('id',$id)->update($cost);
            }    
    
            
    
            if(!empty($nameArr)){
                for($i = 0; $i < count($nameArr); $i++){
                    if(!empty($nameArr[$i])){
    
                        $items = array(
                            'quantity' =>   $qtyArr[$i],
                             'items_id' => $savedArr[$i],
                               'added_by' => auth()->user()->id,
                               'supplier_id' =>   $data['supplier_id'],
                             'purchase_date' =>  $data['purchase_date'],
                               'location' => $data['location'],
                            'purchase_id' =>$id);
                           
         
                         InventoryHistory::create($items);   
          
                        $inv=Inventory::where('id',$nameArr[$i])->first();
                        $q=$inv->quantity + $qtyArr[$i];
                        Inventory::where('id',$nameArr[$i])->update(['quantity' => $q]);
                    }
                }
            
            }    
    
    
            $inv = PurchaseInventory::find($id);
            $supp=Supplier::find($inv->supplier_id);
            $cr= AccountCodes::where('account_name','Inventory')->first();
            $journal = new JournalEntry();
          $journal->account_id = $cr->id;
          $date = explode('-',$inv->purchase_date);
          $journal->date =   $inv->purchase_date ;
          $journal->year = $date[0];
          $journal->month = $date[1];
         $journal->transaction_type = 'inventory';
          $journal->name = 'Inventory Purchase';
          $journal->debit = $inv->purchase_amount *  $inv->exchange_rate;
          $journal->income_id= $inv->id;
           $journal->currency_code =  $inv->exchange_code;
          $journal->exchange_rate= $inv->exchange_rate;
             $journal->notes= "Inventory Purchase with reference no " .$inv->reference_no ." by Supplier ". $supp->name ;
          $journal->save();
        
        if($inv->purchase_tax > 0){
         $tax= AccountCodes::where('account_name','VAT IN')->first();
            $journal = new JournalEntry();
          $journal->account_id = $tax->id;
          $date = explode('-',$inv->purchase_date);
          $journal->date =   $inv->purchase_date ;
          $journal->year = $date[0];
          $journal->month = $date[1];
          $journal->transaction_type = 'inventory';
          $journal->name = 'Inventory Purchase';
          $journal->debit = $inv->purchase_tax *  $inv->exchange_rate;
          $journal->income_id= $inv->id;
           $journal->currency_code =  $inv->exchange_code;
          $journal->exchange_rate= $inv->exchange_rate;
             $journal->notes= "Inventory Purchase Tax with reference no " .$inv->reference_no ." by Supplier ".  $supp->name ;
          $journal->save();
        }
        
          $codes= AccountCodes::where('account_name','Payables')->first();
          $journal = new JournalEntry();
          $journal->account_id = $codes->id;
          $date = explode('-',$inv->purchase_date);
          $journal->date =   $inv->purchase_date ;
          $journal->year = $date[0];
          $journal->month = $date[1];
          $journal->transaction_type = 'inventory';
          $journal->name = 'Inventory Purchase';
          $journal->income_id= $inv->id;
          $journal->credit =$inv->due_amount *  $inv->exchange_rate;
          $journal->currency_code =  $inv->exchange_code;
          $journal->exchange_rate= $inv->exchange_rate;
             $journal->notes= "Credit for Inventory Purchase with reference no " .$inv->reference_no ." by Supplier ".  $supp->name ;
          $journal->save();
    
    
            return redirect(route('purchase_inventory.show',$id));
    

        }

        else{
        $purchase = PurchaseInventory::find($id);
        $data['supplier_id']=$request->supplier_id;
        $data['purchase_date']=$request->purchase_date;
        $data['due_date']=$request->due_date;
        $data['location']=$request->location;
        $data['exchange_code']=$request->exchange_code;
        $data['exchange_rate']=$request->exchange_rate;
        $data['purchase_amount']='1';
        $data['due_amount']='1';
        $data['purchase_tax']='1';
        $data['added_by']= auth()->user()->id;

        $purchase->update($data);
        
        $amountArr = str_replace(",","",$request->amount);
        $totalArr =  str_replace(",","",$request->tax);

        $nameArr =$request->item_name ;
        $qtyArr = $request->quantity  ;
        $priceArr = $request->price;
        $rateArr = $request->tax_rate ;
        $unitArr = $request->unit  ;
        $costArr = str_replace(",","",$request->total_cost)  ;
        $taxArr =  str_replace(",","",$request->total_tax );
        $remArr = $request->removed_id ;
        $expArr = $request->saved_items_id ;
        $savedArr =$request->item_name ;
        
        $cost['purchase_amount'] = 0;
        $cost['purchase_tax'] = 0;

        if (!empty($remArr)) {
            for($i = 0; $i < count($remArr); $i++){
               if(!empty($remArr[$i])){        
                PurchaseItemInventory::where('id',$remArr[$i])->delete();        
                   }
               }
           }

        if(!empty($nameArr)){
            for($i = 0; $i < count($nameArr); $i++){
                if(!empty($nameArr[$i])){
                    $cost['purchase_amount'] +=$costArr[$i];
                    $cost['purchase_tax'] +=$taxArr[$i];

                    $items = array(
                        'item_name' => $nameArr[$i],
                        'quantity' =>   $qtyArr[$i],
                        'tax_rate' =>  $rateArr [$i],
                         'unit' => $unitArr[$i],
                           'price' =>  $priceArr[$i],
                        'total_cost' =>  $costArr[$i],
                        'total_tax' =>   $taxArr[$i],
                         'items_id' => $savedArr[$i],
                           'order_no' => $i,
                           'added_by' => auth()->user()->id,
                        'purchase_id' =>$id);
                       
                        if(!empty($expArr[$i])){
                            PurchaseItemInventory::where('id',$expArr[$i])->update($items);  
      
      }
      else{
        PurchaseItemInventory::create($items);   
      }
                    
                }
            }
            $cost['due_amount'] =  $cost['purchase_amount'] + $cost['purchase_tax'];
            PurchaseInventory::where('id',$id)->update($cost);
        }    

        

        return redirect(route('purchase_inventory.show',$id));

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
        PurchaseItemInventory::where('purchase_id', $id)->delete();
        InventoryPayment::where('purchase_id', $id)->delete();
        InventoryHistory::where('purchase_id', $id)->delete();
        $purchases = PurchaseInventory::find($id);
        $purchases->delete();
        return redirect(route('purchase_inventory.index'))->with(['success'=>'Deleted Successfully']);
    }

    public function findPrice(Request $request)
    {
               $price= Inventory::where('id',$request->id)->get();
                return response()->json($price);	                  

    }

    public function approve($id)
    {
        //
        $purchase = PurchaseInventory::find($id);
        $data['status'] = 1;
        $purchase->update($data);
        return redirect(route('purchase_inventory.index'))->with(['success'=>'Approved Successfully']);
    }

    public function cancel($id)
    {
        //
        $purchase = PurchaseInventory::find($id);
        $data['status'] = 4;
        $purchase->update($data);
        return redirect(route('purchase_inventory.index'))->with(['success'=>'Cancelled Successfully']);
    }

   

    public function receive($id)
    {
        //
        $currency= Currency::all();
        $supplier=Supplier::all();
        $name = Inventory::all();
        $location = Location::all();
        $data=PurchaseInventory::find($id);
        $items=PurchaseItemInventory::where('purchase_id',$id)->get();
        $type="receive";
       return view('inventory.manage_purchase_inv',compact('name','supplier','currency','location','data','id','items','type'));
    }

  public function inventory_list()
    {
        //
        $tyre= InventoryList ::all();
       return view('inventory.list',compact('tyre'));
    }
    public function make_payment($id)
    {
        //
        $invoice = PurchaseInventory::find($id);
        $payment_method = Payment_methodes::all();
        $bank_accounts=AccountCodes::where('account_group','Cash and Cash Equivalent')->get() ;
        return view('inventory.inventory_payment',compact('invoice','payment_method','bank_accounts'));
    }
    
    public function inv_pdfview(Request $request)
    {
        //
        $purchases = PurchaseInventory::find($request->id);
        $purchase_items=PurchaseItemInventory::where('purchase_id',$request->id)->get();

        view()->share(['purchases'=>$purchases,'purchase_items'=> $purchase_items]);

        if($request->has('download')){
        $pdf = PDF::loadView('inventory.purchase_inv_pdf')->setPaper('a4', 'landscape');
         return $pdf->download('PURCHASE_INVENTORY REF NO # ' .  $purchases->reference_no . ".pdf");
        }
        return view('inv_pdfview');
    }

    public function discountModal(Request $request)
    {
                 $id=$request->id;
                 $type = $request->type;
                 if($type == 'refill'){
                    return view('tyre.addtyre',compact('id'));
                
                 }elseif($type == 'assign'){
                    $data =  Truck::find($id);
                    $staff=FieldStaff::all();
                    $name=Tyre::where('status','0')->orwhere('status','2')->get();
                    return view('tyre.addtyre',compact('id','data','staff','name'));   
                 }

                 }

         public function addSupp(Request $request){
       
    
        $supplier= Supplier::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'address' => $request['address'],
            'phone' => $request['phone'],
        'TIN' => $request['TIN'],
            'user_id'=> auth()->user()->id,
        ]);
        
      

        if (!empty($supplier)) {           
            return response()->json($supplier);
         }

       
   }
}
