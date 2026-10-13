<?php

namespace App\Http\Controllers\Tyre;

use App\Http\Controllers\Controller;
use App\Models\AccountCodes;
use App\Models\JournalEntry;
use App\Models\Payment_methodes;
use App\Models\Supplier;
use App\Models\Tyre\PurchaseTyre;
use App\Models\Tyre\TyreActivity;
use App\Models\Tyre\TyrePayment;
use Illuminate\Http\Request;

class TyrePaymentController extends Controller
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

        $receipt = $request->all();
        $sales =PurchaseTyre::find($request->purchase_id);

        if(($receipt['amount'] <= $sales->purchase_amount + $sales->purchase_tax)){
            if( $receipt['amount'] >= 0){
                $receipt['trans_id'] = "TRANS_TYRE-".$request->purchase_id.'-'. substr(str_shuffle(1234567890), 0, 1).'-'.$request->date;
                $receipt['added_by'] = auth()->user()->id;
                
                //update due amount from invoice table
                $data['due_amount'] =  $sales->due_amount-$receipt['amount'];
                if($data['due_amount'] != 0 ){
                $data['status'] = 2;
                }else{
                    $data['status'] = 3;
                }
                $sales->update($data);
                 
                $payment = TyrePayment::create($receipt);

                if(!empty($payment)){
                    $activity = TyreActivity::create(
                        [ 
                            'added_by'=>auth()->user()->id,
                            'module_id'=>$payment->id,
                            'module'=>'Purchase Payment',
                            'activity'=>"Purchase Payment Created",
                            'date'=>$request->date,
                        ]
                        );                      
        }

        $supp=Supplier::find($sales->supplier_id);

        $codes= AccountCodes::where('account_name','Payables')->first();
        $journal = new JournalEntry();
        $journal->account_id = $codes->id;
          $date = explode('-',$request->date);
        $journal->date =   $request->date ;
        $journal->year = $date[0];
        $journal->month = $date[1];
       $journal->transaction_type = 'tire_payment';
        $journal->name = 'Tire Payment';
        $journal->debit =$receipt['amount'] *  $sales->exchange_rate;
          $journal->payment_id= $payment->id;
         $journal->currency_code =   $sales->exchange_code;
        $journal->exchange_rate=  $sales->exchange_rate;
           $journal->notes= "Clear Creditor  with reference no " .$sales->reference_no. " by Supplier ".  $supp->name ; ;
        $journal->save();
  

        $journal = new JournalEntry();
      $journal->account_id = $request->account_id;
      $date = explode('-',$request->date);
      $journal->date =   $request->date ;
      $journal->year = $date[0];
      $journal->month = $date[1];
      $journal->transaction_type = 'tire_payment';
      $journal->name = 'Tire Payment';
      $journal->credit = $receipt['amount'] *  $sales->exchange_rate;
      $journal->payment_id= $payment->id;
       $journal->currency_code =   $sales->exchange_code;
      $journal->exchange_rate=  $sales->exchange_rate;
         $journal->notes= "Payment for Clear Credit  with reference no " .$sales->reference_no. " by Supplier ".  $supp->name ; ;
      $journal->save();


    
                return redirect(route('purchase_tyre.index'))->with(['success'=>'Payment Added successfully']);
            }else{
                return redirect(route('purchase_tyre.index'))->with(['error'=>'Amount should not be equal or less to zero']);
            }
       

        }else{
            return redirect(route('purchase_tyre.index'))->with(['error'=>'Amount should  be less than Purchase amount ']);

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
        $data=TyrePayment::find($id);
        $invoice = PurchaseTyre::find($data->purchase_id);
        $payment_method = Payment_methodes::all();
        $bank_accounts=AccountCodes::where('account_group','Cash and Cash Equivalent')->get() ;
        return view('tyre.tyre_edit_payment',compact('invoice','payment_method','data','id','bank_accounts'));
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

        $payment=TyrePayment::find($id);

        $receipt = $request->all();
        $sales =PurchaseTyre::find($request->purchase_id);
       
        if(($receipt['amount'] <= $sales->purchase_amount + $sales->purchase_tax)){
            if( $receipt['amount'] >= 0){
                $receipt['added_by'] = auth()->user()->id;
                
                //update due amount from invoice table
                if($payment->amount <= $receipt['amount']){
                    $diff=$receipt['amount']-$payment->amount;
                $data['due_amount'] =  $sales->due_amount-$diff;
                }

                if($payment->amount > $receipt['amount']){
                    $diff=$payment->amount - $receipt['amount'];
                $data['due_amount'] =  $sales->due_amount + $diff;
                }
               
                if($data['due_amount'] != 0 ){
                $data['status'] = 2;
                }else{
                    $data['status'] = 3;
                }
                $sales->update($data);
                 
                $payment->update($receipt);

                if(!empty($payment)){
                    $activity = TyreActivity::create(
                        [ 
                            'added_by'=>auth()->user()->id,
                            'module_id'=>$id,
                            'module'=>'Purchase Payment',
                            'activity'=>"Purchase Payment Updated",
                            'date'=>$request->date,
                        ]
                        );                      
        }


        $supp=Supplier::find($sales->supplier_id);

        $codes= AccountCodes::where('account_name','Payables')->first();
        $journal = JournalEntry::where('transaction_type','tire_payment')->where('payment_id', $payment->id)->whereNotNull('debit')->first();
        $journal->account_id = $codes->id;
          $date = explode('-',$request->date);
        $journal->date =   $request->date ;
        $journal->year = $date[0];
        $journal->month = $date[1];
       $journal->transaction_type = 'tire_payment';
        $journal->name = 'Tire Payment';
        $journal->debit =$receipt['amount'] *  $sales->exchange_rate;
          $journal->payment_id= $payment->id;
         $journal->currency_code =   $sales->exchange_code;
        $journal->exchange_rate=  $sales->exchange_rate;
           $journal->notes= "Clear Creditor  with reference no " .$sales->reference_no. " by Supplier ".  $supp->name ; ;
        $journal->update();
  

        $journal = JournalEntry::where('transaction_type','tire_payment')->where('payment_id', $payment->id)->whereNotNull('credit')->first();
      $journal->account_id = $request->account_id;
      $date = explode('-',$request->date);
      $journal->date =   $request->date ;
      $journal->year = $date[0];
      $journal->month = $date[1];
      $journal->transaction_type = 'tire_payment';
      $journal->name = 'Tire Payment';
      $journal->credit = $receipt['amount'] *  $sales->exchange_rate;
      $journal->payment_id= $payment->id;
       $journal->currency_code =   $sales->exchange_code;
      $journal->exchange_rate=  $sales->exchange_rate;
         $journal->notes= "Payment for Clear Credit  with reference no " .$sales->reference_no. " by Supplier ".  $supp->name ; ;
      $journal->update();

                return redirect(route('purchase_tyre.index'))->with(['success'=>'Payment Added successfully']);
            }else{
                return redirect(route('purchase_tyre.index'))->with(['error'=>'Amount should not be equal or less to zero']);
            }
       

        }else{
            return redirect(route('purchase_tyre.index'))->with(['error'=>'Amount should  be less than Purchase amount ']);

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
    }
}
